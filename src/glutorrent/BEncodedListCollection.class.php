<?php
class BEncodedListCollection implements ArrayAccess, Countable, IteratorAggregate {
    protected $Values;

    public function __construct(){
        $this->Values = array();
    }

    public function getIterator(){
        return new BEncodedListCollectionIterator($this->Values);
    }

    public function offsetExists($Index){
        return array_key_exists($Index, $this->Values);
    }

    public function offsetGet($Index){
        if($this->offsetExists($Index)){
            return $this->Values[$Index];
        }else{
            return null;
        }
    }

    public function offsetSet($Index, $Value){
        if(is_numeric($Index) || empty($Index)){
            if($Value instanceof IBEncodedValue || is_scalar($Value) || is_array($Value)){
            if(!empty($Index)){
                if($Index > ($this->count() -1)){
                    throw new BEncodingOutOfRangeException('Attempted to access out of range index '.__CLASS__.'['.$Index.']');
                }
            }elseif(empty($Index)){$Index = count($this->Values);}
            if($Value instanceof IBEncodedValue){
                $this->Values[$Index] = $Value;
            }elseif(is_scalar($Value)){
                if(is_numeric($Value)){
                    $this->Values[$Index] = new BEncodedInteger($Value);
                }else{
                    $this->Values[$Index] = new BEncodedString($Value);
                }
            }elseif(is_array($Value)){
                if(is_numeric(implode('', array_keys($Value)))){
                    $this->Values[$Index] = new BEncodedList($Value);
                }else{
                    $this->Values[$Index] = new BEncodedDictionary($Value);
                }
            }
            }else{
                throw new BEncodingInvalidValueException(__CLASS__.' values must be scalar, arrays or an instance of IBEncodedValue, '.gettype($Value).' supplied');
            }
        }else{
            throw new BEncodingInvalidIndexException(__CLASS__.' Indexes or Keys must be valid integers');
        }
    }

    public function offsetUnset($Index){
        unset($this->Values[$Index]);
    }

    public function Clear(){
        $this->Values = array();
    }

    public function Add($Value){
        $this[] = $Value;
    }

    public function count(){
        return count($this->Values);
    }

    public function GetHashCode(){
        return spl_object_hash($this);
    }
}