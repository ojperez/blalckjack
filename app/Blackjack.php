<?php
namespace App;
/**
 * Main Blackjack Model
 * Here we enforce our 'business' rules
 * and transform our data as needed.
 */
class Blackjack
{
    public $handsTotal;
    public $handsWon; //By player
    public $deck;
    
    public $isRunning;
    
    public $dealerHand;
    public $playerHand;
    
    const DEALER_HIT_THRESHOLD = 16;
    function __construct() 
    {
        $this->isRunning = false;
        $this->wonHand = false; //Who won last hand
    }
    public function getDeck()
    {
        return $this->deck;
    }
    public function new_game()
    {        
        $this->handsTotal = 0;
        $this->handsWon = 0;
        $this->deck = new CardDeck(true);
        $this->new_deal();
        $this->isRunning = true;
        $this->deal();
    }
    
    public function hit($who = 'player')
    {
        if ($who != 'player')
            $who = 'dealer'; //Extra safety
        
        $card = $this->deck->getNextCard();
        /**
         * getNextCard() effectively removes the card from the deck for the game
         * A better approach would be to 'confirm' the returned card was 'used' by
         * the model and not removed from the deck in vain.
         */
        if ($card !== false)
        {        
            if ($who == 'player')
            {
                $card->show();
                $this->playerHand[] = $card;
            } else
            {
                $this->dealerHand[] = $card;              
            }
            $count = $this->countHand($who);
            if ($count >= 21)
            {
                
                if ($count == 21)
                {
                    $this->winTheHand($who);
                } else 
                {
                    $this->winTheHand(($who != 'player')?'player':'dealer');
                }
            }
            return $card;
        } else
        {
            $this->isRunning = false;
            throw new Exception('Card deck not ready!');        
        }
    }
    protected function winTheHand($who)
    {
        /*
         * We flag the hand as having been won
         * by either the player or the dealer.
         */
        
        $this->wonHand = $who;
        if ($who == 'player')
            $this->handsWon+=1;
        if ($who !== false)
        {
            //$who's initial value is false, we can go back to it with
            //this check
            $this->isRunning = false;
            $this->handsTotal+=1;  
            foreach($this->dealerHand as $card)
            {   /*
                 * Someone won, make the dealer show all the cards
                */
                $card->show();
            }
        }        
    }
    public function stand()
    {        
        $countPlayer = $this->countHand('player');
        if ($countPlayer == 21)
        {
            $this->winTheHand('player');
            return;
        } else
        if ($countPlayer > 21)
        {
            $this->winTheHand('dealer');
            return;
        }
        $countDealer = $this->countHand('dealer');
        while(($countDealer <= self::DEALER_HIT_THRESHOLD) ||
                ($countDealer < $countPlayer))
        {
            /*
             * Assuming a 'smart' dealer, that will hit even after its
             * threshold if the player stayed at a higher number.             
             */
            $this->hit ('dealer');
            $countDealer = $this->countHand('dealer');
        }        
        
        if ($countDealer <= 21)
        {
            /* 
            * Changing the > for a >= in the line below defines who to favor
            * in a tie. In this case the player wins.
            */
            if ($countDealer > $countPlayer)
                $this->winTheHand('dealer');
            else
                $this->winTheHand('player');
        }
    }
    
    public function deal()
    {
        $this->new_deal();
        $this->isRunning = true;
        $this->hit('player'); //Hit player twice
        $this->hit('player');
        
        $this->hit('dealer'); //Hit dealer twice
        $this->hit('dealer');
        
        
        /* Show dealer's latest card
         * Notice the semicolon after the foreach
         */
        foreach($this->dealerHand as $idx => $card);
        $this->dealerHand[$idx]->show();
    }
    
    
    public function new_deal()
    {  
        /*
         * Reset hands and the winning marker before dealing a new hand
         */
        $this->dealerHand = [];
        $this->playerHand = [];
        $this->winTheHand(false);
        
    }
    
    
    public function countHand($who = 'player', $highValue = true)
    {
        /**
         * $highValue = Whether to count Aces as 11 (true) or 1 (false)         
         * In the strict sense of things, this is the right place for 
         * this assessment, given that the 1/11 rule is a Blackjack rule,
         * and not a playing cards rule in general.         
         */
        
        $hand = $who == 'player'?$this->playerHand:$this->dealerHand;         
        $total = 0;
        $ace = false; //Whether there's an ace
        foreach($hand as $card)
        {
            $ace = ($ace || ($card->name == 'Ace'));
            $values = $card->getValues();
            $reference = $highValue?0:11; //Reference to measure against
            foreach($values as $value)
            {
                if ($highValue)
                {
                    if ($value > $reference)
                        $reference = $value;
                } else
                {
                    if ($value < $reference)
                        $reference = $value;
                }
            }
            $total+=$reference;
            if ($ace && $highValue && ($total > 21))
            {
                /*
                 * If we have an ace, we are taking the highest values,
                 * and our count goes above 21, let's try counting assuming
                 * the lowest values.
                 * If these multiple values applied to other cards beyond the ace,
                 * theres more efficient algorithms to do this.
                 */
                $total = $this->countHand($who, false); //low value
                break;
            }
        }
        return $total;
    }    
}
