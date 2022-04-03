<?php
class Check{
    public static function checkkk(string $str)
    {
        for ($i = 0; $i < strlen($str); $i++) {
            if (ctype_digit($str[$i])) {
                return false;
            }
        }
        return true;
    }
}