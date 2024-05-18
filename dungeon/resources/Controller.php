<?php

use function PHPSTORM_META\elementType;

class Controller {
    private $data;
    private Dungeon $dungeon;
    private bool $gameStatus;
    public function __construct(){
        $this->gameStatus = false;
    }
    public function proccesRequest($method, $request){
        $this->data = json_decode(file_get_contents('php://input'), true);
        if (!$method == 'POST'){
            http_response_code(400);
            exit;
        }
        switch ($request){
            case 'start':
                $this->startGame();
                break;
            case 'move':
                $this->move();
                break;
            case 'fight':
                $this->fight();
                break;
            default:
                http_response_code(404);
                exit;
        }
    }
    private function validateDungeon(): bool{
        $n = null;
        if (isset($this->data['n'])){
            $n = intval($this->data['n']);
        }
        else 
            return false;

        if (isset($this->data['start'])){
            if (intval($this->data['start']) < 0 || 
            intval($this->data['start']) >= $n )
                return false;
        }
        else
            return false;

        if (isset($this->data['finish'])){
            if (intval($this->data['finish']) < 0 || 
            intval($this->data['finish']) >= $n )
                return false;
        }
        else
            return false;

        if (isset($this->data['rooms'])){
            if (count($this->data['rooms']) != $n)
                return false;
        }
        else
            return false;

        if (isset($this->data['routes'])){
            foreach ($this->data['routes'] as $curRoutes){
                foreach ($curRoutes as $cur){
                    if ($cur >= $n || $cur < 0)
                        return false;
                }
            }
        }
        else
            return false;
        return true;
    }
    private function startGame(){
        try{
            if (!$this->validateDungeon()){
                http_response_code(400);
                exit;
            }
            $this->dungeon = new Dungeon($this->data['n'], $this->data['start'],
            $this->data['finish'], $this->data['rooms'], $this->data['routes']);
            echo json_encode($this->dungeon->startGame());
            http_response_code(200);
            $this->gameStatus = true;
        }
        catch (Exception $e){
            http_response_code(400);
        }
        exit;
    }

    private function move(){
        if (!$this->gameStatus || intval($this->data['target']) >= $this->dungeon->getLength()){
            http_response_code(400);
            exit;
        }
        $info = $this->dungeon->move(intval($this->data['target']));
        if (isset($info['finalScore']))
            $this->gameStatus = false;
        echo json_encode($info);
        http_response_code(200);
        exit;
    }

    private function fight(){
        if (!$this->gameStatus){
            http_response_code(400);
            exit;
        }
        $info = $this->dungeon->fight();
        if (isset($info['finalScore']))
            $this->gameStatus = false;
        echo json_encode($info);
        http_response_code(200);
        exit;
    }
}