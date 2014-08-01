<?php
class BEncodedDictionaryCollection implements ArrayAccess, Countable, IteratorAggregate {
    /**
     * Collection of Keys for associative dictionary
     * These are stored in string format rather than native BEncodedStrings due to php shortfallings
     *
     * @var string[]
     */
    protected $Keys;
    /**
     * Collection of Values for associative dictionary
     *
     * @var IBEncodedValue[]
     */
    protected $Values;

    /**
     * Initialise Key Value collections
     *
     */
    public function __construct(){
        $this->Keys = array();
        $this->Values = array();
    }

    /**
     * Implementation of IteratorAggregate
     *
     * @return BEncodedDictionaryCollectionIterator
     */
    public function getIterator(){
        return new BEncodedDictionaryCollectionIterator($this->Keys, $this->Values);
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $Index
     * @return bool
     */
    public function offsetExists($Index){
        return array_key_exists($this->GetIndexHash($Index), $this->Keys);
    }

    /**
     * Implementation of ArrayAccess
     *
     * @param mixed $Index
     * @return object
     */
    public function offsetGet($Index){
        if($this->offsetExists($Index)){
            return $this->Values[$this->GetIndexHash($Index)];
        }else{
            return null;
        }
    }

    /**
     * Implementation of ArrayAccess
     * This method has been modified to accept untyped input, such as (int), (string) and cast to the respective IBEncodedValue type
     * Keys must be scalar or an instance of BEncodedString
     * The collection will automatically be sorted upon any call to this method unless an exception is thrown
     *
     * @example offsetSet('someKey', 'stringValue');
     * @example offsetSet('intVal', 1);
     * @example offsetSet(new BEncodedString('Integer'), new BEncodedInteger(6969));
     * @example offsetSet(new BEncodedString('key'), new BEncodedList(array(1,2,3));
     * 
     * @param mixed $Index
     * @param mixed $Value
     */
    public function offsetSet($Index, $Value){
        if($Value instanceof IBEncodedValue || is_scalar($Value) || is_array($Value)){
            $Hash = $this->GetIndexHash($Index);
            if($Index instanceof BEncodedString){
                $this->Keys[$Hash] = $Index;
            }else{
                $this->Keys[$Hash] = new BEncodedString($Index);
            }
            if($Value instanceof IBEncodedValue){
                $this->Values[$Hash] = $Value;
            }elseif(is_scalar($Value)){
                if(is_numeric($Value)){
                    $this->Values[$Hash] = new BEncodedInteger($Value);
                }else{
                    $this->Values[$Hash] = new BEncodedString($Value);
                }
            }elseif(is_array($Value)){
                if(is_numeric(implode('', array_keys($Value)))){
                    $this->Values[$Hash] = new BEncodedList($Value);
                }else{
                    $this->Values[$Hash] = new BEncodedDictionary($Value);
                }
            }
        }else{
            throw new BEncodingInvalidValueException(__CLASS__.' values must be scalar, arrays or an instance of IBEncodedValue, '.gettype($Value).' supplied');
        }
        array_multisort($this->Keys, SORT_ASC, SORT_STRING, $this->Values);
    }

    /**
     * Implementation of ArrayAccess
     * The collection will automatically be sorted upon any call to this method
     * 
     * @param mixed $Index
     */
    public function offsetUnset($Index){
        $Hash = $this->GetIndexHash($Index);
        unset($this->Keys[$Hash]);
        unset($this->Values[$Hash]);
        array_multisort($this->Keys, SORT_ASC, SORT_STRING, $this->Values);
    }

    /**
     * Clears the collection
     *
     */
    public function Clear(){
        $this->Keys = array();
        $this->Values = array();
    }

    /**
     * Helper method to add values
     * Wrapper for @see BEncodedDictionaryCollection::offsetSet()
     *
     * @param unknown_type $Index
     * @param unknown_type $Value
     */
    public function Add($Index, $Value){
        $this->offsetSet($Index, $Value);
    }

    /**
     * Implementation of Countable
     * Returns the number of values in this colleciton
     *
     * @return int
     */
    public function count(){
        return count($this->Values);
    }

    /**
     * Implementation of IGenericObject
     * Return a unique hash for this object
     * 
     * @return string
     */
    public function GetHashCode(){
        return spl_object_hash($this);
    }

    /**
     * Generate the hash of an index value
     *
     * 
     * @param mixed $Index
     * @return string
     */
    private function GetIndexHash($Index){
        if(is_scalar($Index)){
            return md5($Index);
        }elseif($Index instanceof BEncodedString){
            return $Index->GetHashCode();
        }else{
            throw new BEncodingInvalidIndexException(__CLASS__.' Indexes or Keys must be scalar or an instance of BEncodedString');
        }
    }
}
?>