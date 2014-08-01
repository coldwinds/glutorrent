<?php
class BEncodedInteger implements IBEncodedValue {
    public $Value;
    
    public function __construct($Value = null){
        if(is_null($Value)){
            $this->Value = null;
        }else{
            if(is_numeric($Value)){
                $this->Value = intval($Value);
            }else{
                throw new BEncodingInvalidValueException(__CLASS__.' cannot be created with non-numeric default value ('.gettype($Value).')');
            }
        }    
    }
    
    public function FromString($BEncodedString){
        $Offset = 0;
        $this->Parse($BEncodedString, $Offset);
        if($Offset != strlen($BEncodedString)){
            throw new BEncodingParserException('Unknown error parsing '.__CLASS__, $BEncodedString, $Position);
        }
    }
    
    public function TryParse($BEncodedString){
        try{
            $this->FromString($BEncodedString);
        }catch(Exception $e){
            return false;
        }
        return true;
    }
    
    public function Parse(&$BEncodedString, &$Offset){
        if($BEncodedString{$Offset} == 'i'){
            $Offset += 1;
            $ValueEnd = strpos($BEncodedString, 'e', $Offset);
            if($ValueEnd === false || $Offset < $ValueEnd){
                $ValueEnd -= $Offset;
                $Value = substr($BEncodedString, $Offset, $ValueEnd);
                if(is_numeric($Value)){
                    $Offset += strlen($Value) + 1;
                    $this->Value = intval($Value);
                }else{
                    throw new BEncodingParserException(__CLASS__.' encountered non-numeric value', $BEncodedString, $Offset);
                }
            }else{
                throw new BEncodingParserException(__CLASS__.' could not locate field delimiter');
            }
        }else{
            throw new BEncodingParserException(__CLASS__.' encountered unrecognised encoding', $BEncodedString, $Offset);
        }
    }
    
    public function Encode(){
        return 'i'.strval($this->Value).'e';
    }
    
    public function GetHashCode(){
        return spl_object_hash($this);
    }
    
    public function ToString(){
        return $this->__toString();
    }
    
    public function __toString(){
        return strval($this->Value);
    }
}
?>