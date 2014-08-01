<?php
final class BEncodingInvalidValueException extends BEncodingException {
    public function __construct($Message){
        parent::__construct($Message);
    }
}
?>