<?php
final class BEncodingOutOfRangeException extends BEncodingException {
    public function __construct($Message){
        parent::__construct($Message);
    }
}
?>