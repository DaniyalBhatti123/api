<?php
    require_once("vendor/Paris/idiorm.php");
    require_once("vendor/Paris/paris.php");
    $ENVIROMENT = 'PRODUCTION';
    // $ENVIROMENT = 'DEVELOPMENT';
    if($ENVIROMENT == 'PRODUCTION') {
        ORM::configure('mysql:host=localhost;dbname=aonedeco_db');
        ORM::configure('username', 'aonedeco_user');
        ORM::configure('password', '[K".{h*7@1k]');
        ORM::configure('logging', true);
    } else {
        ORM::configure('mysql:host=localhost;dbname=aone_decor');
        ORM::configure('username', 'root');
        ORM::configure('password', '');
        ORM::configure('logging', true);
    }
?>