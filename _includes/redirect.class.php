<?php
class Redirect {
    public static function redir(){
        header('Location: index.php');
        exit();
    }
}