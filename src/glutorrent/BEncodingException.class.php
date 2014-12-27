<?php
abstract class BEncodingException extends Exception {
    protected $InnerException;
    
    public function __construct($Message){
        parent::__construct($Message);
        $this->InnerException = null;
    }
    
    public function &getInnerException(){
        return $this->InnerException;
    }

    /**
     * The __toString magic method can no longer accept arguments.
     *
     * http://php.net/manual/en/migration53.incompatible.php
     *
     * @return string
     */
    public function __toString(){
        $TabSpace = '';
        $ExceptionText = "\n";
        $ExceptionText .= $TabSpace.get_class($this)." Thrown in\n";
        $ExceptionText .= $TabSpace.'File: '.basename($this->getFile())."\n";
        $ExceptionText .= $TabSpace.'Line: '.$this->getLine()."\n";
        $ExceptionText .= $TabSpace.$this->getMessage()."\n";
        $ExceptionText .= "\n";
        if(is_null($this->InnerException) === false){
            $ExceptionText .= "Inner Exception\n";
            $ExceptionText .= $this->InnerException->__toString($TabSpace."\t\t");
            $ExceptionText .= "\n\n";
        }
        foreach($this->getTrace() as $CallTrace){
            $ExceptionText .= $TabSpace.basename($CallTrace['file']).': '.$CallTrace['line']."\n";
            $ExceptionText .= $TabSpace.'  ';
            if(isset($CallTrace['class'])){
                $ExceptionText .= $CallTrace['class'].'::';
            }
            $ExceptionText .= $CallTrace['function'].'('.implode(', ', $CallTrace['args']).")\n";
        }
        return $ExceptionText;
    }
}
?>