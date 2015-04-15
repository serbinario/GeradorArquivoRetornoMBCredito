<?php

class PDOConfig extends PDO 
{   
    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;
   
    public function __construct()
    {
        $this->engine = 'mysql';
        $this->host = 'localhost';
        $this->database = 'mbcredito';
        $this->user = 'root';
        //$this->pass = 'root';
        $this->pass = 'w6zAh*tr';
        $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
        parent::__construct( $dns, $this->user, $this->pass, array());
    }
}