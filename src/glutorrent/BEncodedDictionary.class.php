<?php
class BEncodedDictionary extends BEncodedDictionaryCollection implements IBEncodedValue {

    public function __construct($Values = null){
        parent::__construct();
        if(!is_null($Values)){
            foreach($Values as $Key=>$Value){
                $Key = new BEncodedString($Key);
                if($Value instanceof IBEncodedValue){
                    $this->Add($Key, $Value);
                }elseif(is_scalar($Value)){
                    if(is_numeric($Value)){
                        $this->Add($Key, new BEncodedInteger($Value));
                    }else{
                        $this->Add($Key, new BEncodedString($Value));
                    }
                }elseif(is_array($Value)){
                        if(is_numeric(implode('', array_keys($Value)))){
                            $this->Add($Key, new BEncodedList($Value));
                        }else{
                            $this->Add($Key, new BEncodedDictionary($Value));
                        }
                }else{
                    throw new Exception('Unable to parse non-BEncoded value');
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
        if($BEncodedString{$Offset} == 'd'){
            $Offset += 1;
            $Key = null;
            $Value = null;
            while((substr($BEncodedString, $Offset, 1) === 'e') === false){
                $TmpOffset = $Offset;
                try{
                    $Key = new BEncodedString();
                    $Key->Parse($BEncodedString, $TmpOffset);
                }catch(BEncodingParserException $e){
                    throw new BEncodingParserException(__CLASS__.' expected BEncodedString Index or Key at offset '.$Offset, $BEncodedString, $Offset, $e);
                }
                $Offset = $TmpOffset;
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
                $this[$Key] = $Value;
            }
            $Offset += 1;
        }else{
            throw new BEncodingParserException(__CLASS__.' encountered unrecognised encoding', $BEncodedString, $Offset);
        }
    }

    public function Encode(){
        $Encode = 'd';
        foreach($this->Values as $Hash=>$Value){
            $Encode .= $this->Keys[$Hash]->Encode();
            $Encode .= $Value->Encode();
        }
        $Encode .= 'e';
        return $Encode;
    }

    public function ToArray(){
        $ArrayValues = array();
        foreach($this->Values as $Hash=>$Value){
            if($Value instanceof BEncodedList || $Value instanceof BEncodedDictionary){
                $ArrayValues[$this->Keys[$Hash]->ToString()] = $Value->ToArray();
            }else{
                $ArrayValues[$this->Keys[$Hash]->ToString()] = $Value->Value;
            }
        }
        return $ArrayValues;
    }
    
    public function ToString(){
        return $this->__toString();
    }
    
    public function __toString(){
        return __CLASS__.'['.count($this).']';
    }
}
?>