<?php
class BEncodedListCollectionIterator implements Iterator {
    private $Values;
    
    public function __construct($Values){
        $this->Values = $Values;
    }
    
    public function rewind(){
        reset($this->Values);
    }
    
    public function current(){
        return current($this->Values);
    }
    
    public function key(){
        return key($this->Values);
    }
    
    public function next(){
        return next($this->Values);
    }
    
    public function valid(){
        return $this->current() == false ? false : true;
    }
}
?>