<?php
   define('Base_url', 'https://api-oc.herokuapp.com/');
   define('File_path', "audio/");
   require_once("vendor/Paris/idiorm.php");
   require_once("vendor/Paris/paris.php");
   $ENVIROMENT = 'PRODUCTION';
   //$ENVIROMENT = 'DEVELOPMENT';
   if($ENVIROMENT == 'PRODUCTION') {
       ORM::configure('mysql:host=localhost;dbname=easywayq_orvba');
       ORM::configure('username', 'easywayq_orvba_user');
       ORM::configure('password', 'WA,=vil[PB][');
       ORM::configure('logging', true);
   } else {
       ORM::configure('mysql:host=localhost;dbname=orvba_db');
       ORM::configure('username', 'root');
       ORM::configure('password', '');
       ORM::configure('logging', true);
   }
?>