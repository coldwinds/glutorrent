<?php
class BEncodedString implements IBEncodedValue {
    public $Value;
    
    public function __construct($Value = null){
        if(is_null($Value)){
            $this->Value = '';
        }else{
            if(is_scalar($Value)){
                $this->Value = strval($Value);
            }else{
                throw new BEncodingInvalidValueException('BEncodedString cannot be created with non-scalar default value ('.gettype($Value).')');
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
        $LengthEnd = strpos($BEncodedString, ':', $Offset);
        if($LengthEnd === false || $Offset < $LengthEnd){
            $LengthEnd -= $Offset;
            $Length = substr($BEncodedString, $Offset, $LengthEnd);
            if(is_numeric($Length)){
                $Offset += strlen($Length) + 1;
                $Length = intval($Length);
                $this->Value = strval(substr($BEncodedString, $Offset, $Length));
                $Offset += $Length;
            }else{
                throw new BEncodingParserException(__CLASS__.' encountered unrecognised encoding', $BEncodedString, $Offset);
            }
        }else{
            throw new BEncodingParserException(__CLASS__.' could not find length delimiter');
        }
    }

    public function Encode(){
        return strlen($this->Value).':'.$this->Value;
    }

    public function GetHashCode(){
        return md5($this->Value);
    }

    public function ToString(){
        return $this->__toString();
    }

    public function __toString(){
        return $this->Value;
    }
}
?>