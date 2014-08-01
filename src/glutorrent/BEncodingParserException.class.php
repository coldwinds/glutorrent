<?php
final class BEncodingParserException extends BEncodingException {
    private $BEncodedString;
    private $Offset;
    
    public function __construct($Message, $BEncodedString = null, $Offset = null, Exception $InnerException = null){
        parent::__construct($Message);
        $this->BEncodedString = $BEncodedString;
        $this->Offset = $Offset;
        $this->InnerException = $InnerException;
    }
}
?>