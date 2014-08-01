<?php
interface IBEncodedValue {
    public function Encode();
    public function GetHashCode();
    public function ToString();
    public function __toString();
}
?>