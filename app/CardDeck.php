<?php
namespace App;

class CardDeck
{
    const COUNT = 52;
    const VALUES = 
    [
        'a'=>['values' => [1,11], 'name' => 'Ace', 'unicode' => '1'],
        '2'=>['values' => [2], 'name' => 'Two', 'unicode' => '2'],
        '3'=>['values' => [3], 'name' => 'Three', 'unicode' => '3'],
        '4'=>['values' => [4], 'name' => 'Four', 'unicode' => '4'],
        '5'=>['values' => [5], 'name' => 'Five', 'unicode' => '5'],
        '6'=>['values' => [6], 'name' => 'Six', 'unicode' => '6'],
        '7'=>['values' => [7], 'name' => 'Seven', 'unicode' => '7'],
        '8'=>['values' => [8], 'name' => 'Eight', 'unicode' => '8'],
        '9'=>['values' => [9], 'name' => 'Nine', 'unicode' => '9'],
        '10'=>['values' => [10], 'name' => 'Ten', 'unicode' => 'A'],
        'j'=>['values' => [10], 'name' => 'Jack', 'unicode' => 'B'],
        'q'=>['values' => [10], 'name' => 'Queen', 'unicode' => 'D'],
        'k'=>['values' => [10], 'name' => 'King', 'unicode' => 'E']        
    ];
    const SUITS =
    [
        'c' => ['suit' => 'Clubs', 'unicode' => '1F0D'],  
        'd' => ['suit' => 'Diamonds', 'unicode' => '1F0C'],
        'h' => ['suit' => 'Hearts', 'unicode' => '1F0B'],
        's' => ['suit' => 'Spades', 'unicode' => '1F0A'],
    ];
    
    public $deck;
    public $dealt_cards;
    public $ready; //Is the deck ready to be used
    
    function __construct($new_deck_and_shuffle = false) 
    {
        $this->ready = false;
        $this->deck = [];
        $this->dealt_cards = [];
        if ($new_deck_and_shuffle)
            $this->new_deck_and_shuffle();
    }
    public function new_deck_and_shuffle()
    {
        $cnt = 0;
        foreach (self::SUITS as $i => $suit)
        {
            foreach (self::VALUES as $j => $value)
            {
                if ($cnt < self::COUNT)
                {
                    /* RE: $cnt
                     * Kind of overkill since our suits and values are hardcoded.
                     * A must if our values/suits came from an external source.
                     */
                    $this->deck[$j.$i] = new PlayingCard($j.$i, $value['name'], $value['values'], $suit['suit'], $suit['unicode'].$value['unicode']); 
                    $cnt++;
                }
            }
        }
        shuffle($this->deck);
        $this->ready = true;
    }
    public function getNextCard()
    {
        if (!$this->ready)
            return false;
        
        /* Here's an unusual way of getting the first item of an array :) */
        foreach($this->deck as $idx => $card)
            break;
        $this->dealt_cards[$idx] = $card;
        unset($this->deck[$idx]);
        $this->ready = (count($this->deck) > 0);
        return $card;
    }
    public function percentageUsed()
    {
        /*
         * Not rounded, must be rounded by controller or view before being presented
         * to the user.
         */
        $deckCount = count($this->deck);
        return ($deckCount > 0)?(count($this->dealt_cards) * 100 / $deckCount):0;
    }
}
