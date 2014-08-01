<?php
final class BEncodingInvalidIndexException extends BEncodingException {
    public function __construct($Message){
        parent::__construct($Message);
    }
}
?>