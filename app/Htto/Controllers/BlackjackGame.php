<?php
namespace App\Http\Controllers;
/**
 * Main Blackjack Controller
 */
use App\Blackjack;
use Illuminate\Http\Request;

class BlackjackGame extends Controller
{
    public $game;
    const PERC_TO_STOP_GAME = 60;
    /*
     * Static methods to retrieve and store a 'game'
     * Implementation of the following 2 methods could change
     * to store games in a database, etc.
     *     
     */ 
    private static function retrieveGame($name)
    {
        if (isset($_SESSION[$name]))
        {
            return unserialize($_SESSION[$name]);
        } else
        {
            return new \App\Blackjack();
        }        
    }
    private static function storeGame($name, $game)
    {
        $_SESSION[$name] = serialize($game);
    }
    private static function clearGame($name)
    {
        if (isset($_SESSION[$name]))
        {
            unset($_SESSION[$name]);
        }
    }
    /*****************************************************************/
    
    function __construct() 
    {
        if (session_status() != PHP_SESSION_ACTIVE)
        {
            session_start();
        }
        $this->game = self::retrieveGame('game');
        
    }
    
    public function index()
    { 
        /*
         * Ideal place to load a welcome page.
         * For now, we just start a new game right away
         */
        return $this->new_game();        
    }
    public function new_game()
    {   
        /*
         * We make sure we start with a brand new \App\Blackjack instance.
         * Alternatively, the model could have a method to 'reset' itself.
         */
        self::clearGame('game');
        $this->game = self::retrieveGame('game');
        $this->game->new_game();
        self::storeGame('game', $this->game);        

        return $this->load_game_view(self::getActions('new_game',$this->game));
    }
    public function hit()
    {
        if ($this->game->isRunning)
        {
            try
            {
                $this->game->hit('player');
            } catch (Exception $ex)
            {
                /*
                 * Just restart the game if something goes wrong with the deck.
                 * Better error handling is possible, of course.
                 */                
                return $this->new_game();
            }
            self::storeGame('game', $this->game);        
        }
        return $this->load_game_view(self::getActions('hit',$this->game));
    }
    public function stand()
    {
        if ($this->game->isRunning)
        {
            $this->game->stand();            
            self::storeGame('game', $this->game);        
        }
        return $this->load_game_view(self::getActions('stand',$this->game));
    }
    public function deal()
    {
        if (!$this->game->isRunning)
        {
            $this->game->deal();            
            self::storeGame('game', $this->game);        
        }
        return $this->load_game_view(self::getActions('deal',$this->game));
    }   
    private function load_game_view($actions)
    {
        /**
         * Common view loader for the game.
         * We only pass needed information to the view.
         */
        $won = $this->hasSomeoneWon();
        return view('blackjack', ['actions'=>$actions, 
            'dealerHand' => $this->game->dealerHand, 'playerHand' => $this->game->playerHand, 
            'handsTotal' => $this->game->handsTotal, 'handsWon' => $this->game->handsWon, 'wonHand' => $won['hand'], 'wonGame' => $won['game']]);
    }
    public function hasSomeoneWon()
    {
        /*
         * Determine if either the player or the dealer have won the hand and/or the game
         */
        return [
        'game' => ($this->game->getDeck()->percentageUsed() >= self::PERC_TO_STOP_GAME)&&(!$this->game->isRunning)?'The game has ended, we used '.round($this->game->getDeck()->percentageUsed(), 0).'% of the total cards in the deck. You won '.$this->game->handsWon.' out of '.$this->game->handsTotal.' hands.':false,
        'hand' => (!$this->game->isRunning && $this->game->wonHand !=false)?$this->game->wonHand:false];
    }
    public static function getActions($last_action, $game)
    {
        /*
         * Return all possible actions given last action and current state
         * of the game.
         */
        $actions = [];
        if ($last_action == '')
        {
            $actions = ['new_game' => 'New Game'];
        } else 
        if ($last_action == 'new_game')
        {
            $actions = ['hit' => 'Hit', 'stand' => 'Stand'];
        } else 
        if ($last_action == 'hit')
        {
            if (!$game->isRunning)
            {
                $actions = ['new_game' => 'New Game'];
                if ($game->getDeck()->percentageUsed() < self::PERC_TO_STOP_GAME)
                {
                    $actions['deal'] = 'Deal';
                }
            } else
            {
                $actions = ['hit' => 'Hit', 'stand' => 'Stand'];
            }
            
        } else 
        if ($last_action == 'stand')
        {
            $actions = ['new_game' => 'New Game'];
            if ($game->getDeck()->percentageUsed() < self::PERC_TO_STOP_GAME)
            {
                $actions['deal'] = 'Deal';
            }
        } else         
        if ($last_action == 'deal')
        {
            if (!$game->isRunning)
            {
                $actions = ['new_game' => 'New Game'];
                if ($game->getDeck()->percentageUsed() < self::PERC_TO_STOP_GAME)
                {
                    $actions['deal'] = 'Deal';
                }
                
            } else
            {
                $actions = ['hit' => 'Hit', 'stand' => 'Stand'];
            }
        }        
        return $actions;        
    }

}