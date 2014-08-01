<?php
class BEncodedList extends BEncodedListCollection implements IBEncodedValue {
    
    public function __construct($Values = null){
        parent::__construct();
        if(!is_null($Values)){
            foreach($Values as $Value){
                if($Value instanceof IBEncodedValue){
                    $this->Add($Value);
                }elseif(is_numeric($Value)){
                    $this->Add(new BEncodedInteger($Value));
                }elseif(is_string($Value)){
                    $this->Add(new BEncodedString($Value));
                }elseif(is_array($Value)){
                        if(is_numeric(implode('', array_keys($Value)))){
                            $this->Add(new BEncodedList($Value));
                        }else{
                            $this->Add(new BEncodedDictionary($Value));
                        }
                }else{
                    throw new BEncodingInvalidValueException(__CLASS__.' cannot be created with '.gettype($Values).' as a default value');
                }
            }
        }
    }
    
    public function FromString($BEncodedString){
        $Offset = 0;
        $this->Parse($BEncodedString, $Offset);
        if($Offset != strlen($BEncodedString)){
            throw new BEncodingParserException('Unknown error parsing '.__CLASS__, $BEncodedString, $Offset);        
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
        if($BEncodedString{$Offset} == 'l'){
            $Offset += 1;
            $Value = null;
            while((substr($BEncodedString, $Offset, 1) === 'e') === false){
                $TmpOffset = $Offset;
                switch(true){
                    case $BEncodedString{$Offset} === 'd':
                        try{
                            $Value = new BEncodedDictionary();
                            $Value->Parse($BEncodedString, $TmpOffset);
                        }catch(BEncodingParserException $e){
                            throw new BEncodingParserException(__CLASS__.' expected BEncodedDictionary at offset '.$Offset, $BEncodedString, $Offset, $e);
                        }
                        $Offset = $TmpOffset;
                        break;
                    case $BEncodedString{$Offset} === 'l':
                        try{
                            $Value = new BEncodedList();
                            $Value->Parse($BEncodedString, $TmpOffset);
                        }catch(BEncodingParserException $e){
                            throw new BEncodingParserException(__CLASS__.' expected BEncodedList at offset '.$Offset, $BEncodedString, $Offset, $e);
                        }
                        $Offset = $TmpOffset;
                        break;
                    case $BEncodedString{$Offset} === 'i':
                        try{
                            $Value = new BEncodedInteger();
                            $Value->Parse($BEncodedString, $TmpOffset);
                        }catch(BEncodingParserException $e){
                            throw new BEncodingParserException(__CLASS__.' expected BEncodedInteger at offset '.$Offset, $BEncodedString, $Offset, $e);
                        }
                        $Offset = $TmpOffset;
                        break;
                    case is_numeric($BEncodedString{$Offset}):
                        try{
                            $Value = new BEncodedString();
                            $Value->Parse($BEncodedString, $TmpOffset);
                        }catch(BEncodingParserException $e){
                            throw new BEncodingParserException(__CLASS__.' expected BEncodedString at offset '.$Offset, $BEncodedString, $Offset, $e);
                        }
                        $Offset = $TmpOffset;
                        break;
                    default:
                        throw new Exception(__CLASS__.' encountered unexpected token: "'.$BEncodedString{$Offset});
                }
                $this[] = $Value;
            }
        }else{
            throw new BEncodingParserException(__CLASS__.' encountered unrecognised encoding', $BEncodedString, $Offset);
        }
        $Offset += 1;
    }
    
    public function Encode(){
        $Encode = 'l';
        foreach($this->Values as $Value){
            $Encode .= $Value->Encode();
        }
        $Encode .= 'e';
        return $Encode;
    }
    
    public function ToArray(){
        $ArrayValues = array();
        foreach($this->Values as $Value){
            if($Value instanceof BEncodedList || $Value instanceof BEncodedDictionary){
                $ArrayValues[] = $Value->ToArray();
            }else{
                $ArrayValues[] = $Value->Value;
            }
        }
        return $ArrayValues;
    }
    
    public function ToString(){
        return $this->__toString();
    }
    
    public function __toString(){
        return __CLASS__.'['.$this->count().']';
    }
}
?>