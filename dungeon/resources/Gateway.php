<?php

class Gateway {

    public function __construct(private $host, private $name, private $user, private $password){}
    public static function insertScore(int $score): void{
        $conn = new PDO("mysql:host=localhost;dbname=dungeon", 'root', '');
        $sql = "INSERT INTO results (score) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($score));
    }

}