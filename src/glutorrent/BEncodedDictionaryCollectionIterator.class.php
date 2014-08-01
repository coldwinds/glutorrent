<?php
class BEncodedDictionaryCollectionIterator implements Iterator {
    private $Keys;
    private $Values;
    private $Position;
    
    public function __construct($Keys, $Values){
        $this->Keys = $Keys;
        $this->Values = $Values;
        $this->Position = 0;
    }
    
    public function rewind(){
        reset($this->Values);
    }
    
    public function current(){
        return current($this->Values);
    }
    
    public function key(){
        return $this->Keys[key($this->Values)]->ToString();
    }
    
    public function next(){
        return next($this->Values);
    }
    
    public function valid(){
        return $this->current() == false ? false : true;
    }
}
?>