<?php
namespace App;

class PlayingCard
{
    public $code;
    public $name;
    public $values;
    public $suit;
    public $unicode;
    public $status;
    
    function __construct($code, $name, $values, $suit, $unicode) 
    {
        $this->status = 'hidden'; //Has card been shown by dealer?
        $this->code = $code;
        $this->name = $name;
        $this->values = $values;
        $this->unicode = $unicode;
        $this->suit = $suit;
    }
    public function getValues()
    {
        return (is_array($this->values))?$this->values:[];
    }
    public function getFullName()
    {
        return $this->name.' of '.$this->suit;
    }
    public function show()
    {
        $this->status = 'shown';
        return $this->toHTML();
    }
    public function imageURI()
    {
        return ($this->status == 'hidden')?asset('img/hidden.jpg'):asset('img/'.$this->code.'.jpg');
    }
    public function toHTML()
    {
        /*
         * Experimented using UNICODE representations of playing cards.
         * Fell back to images as the UNICODEs were not being rendered in mobile.
         */
//      return ($this->status == 'hidden')?'&#x1F0A0;':('&#x'.$this->unicode.';');
        return '<img src="'.$this->imageURI().'">';
    }
}
