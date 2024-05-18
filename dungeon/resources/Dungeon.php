<?php

class Dungeon{
    private int $length, $start, $finish, $pos, $score, $curMonsterScore;
    private array $rooms;
    private array $doors;
    private array $monsterHP;
    public function __construct(int $length, int $start, int $finish, array $rooms, array $doors){
        $this->length = $length;
        $this->start = $start;
        $this->finish = $finish;
        $this->rooms = $rooms;
        $this->doors = $doors;
        $this->pos = $start;
        $this->score = 0;

        for ($i = 0; $i < $length; $i++){
            $curRoom = explode(" ", $this->rooms[$i]);
            if ($curRoom[0] == 'monster')
                $this->monsterHP[$i] = intval($curRoom[1]);
            else 
                $this->monsterHP[$i] = 0;
        }

    }
    private function chestPrize(string $type): int{
        $prize = 0;
        switch ($type){
            case 'common':
                $prize = random_int(1, 10);
                break;
            case 'rare':
                $prize = random_int(1, 25);
                break;
            case 'epic':
                $prize = random_int(1, 100);
                break;
        }
        return $prize;
    }
    private function getRoomInfo(): array{
        $info = array();
        $curRoom = explode(" ", $this->rooms[$this->pos]);
        $info['location'] = $this->pos;
        $availableMoves = $this->doors[$this->pos];
        switch ($curRoom[0]){
            case 'empty':
                $info['status'] = 'empty room';
                break;
            case 'chest':
                $prize = $this->chestPrize($curRoom[1]);
                $this->score += $prize;
                $info['status'] = "chest +{$prize} points";
                $this->rooms[$this->pos] = 'empty';
                break;
            case 'monster':
                $info['status'] = "monster with {$curRoom[1]}HP";
                $availableMoves = array();
                break;
        }
        $info['currentScore'] = $this->score;
        $info['availableMoves'] = $availableMoves;
        if ($this->checkFinish())
            return $this->goToFinish();
        return $info;
    }
    private function checkFinish(): bool{
        if ($this->pos == $this->finish && $this->rooms[$this->pos] == 'empty')
            return true;
        else
            return false;
    }
    public function getLength(): int{
        return $this->length;
    }
    public function getPos(): int{
        return $this->pos;
    }
    public function getScore(): int{
        return $this->score;
    }
    public function getMoves(int $pos): array{
        if ($pos < $this->length)
            return $this->doors[$pos];
        else 
            return array();
    }
    public function startGame(): array{
        if ($this->pos == $this->start)
            return $this->getRoomInfo();
        return array();
    }
    public function move(int $tar): array{
        $f = false;
        $curRoom = explode(" ", $this->rooms[$this->pos]);
        foreach ($this->doors[$this->pos] as $loc){
            if ($loc == $tar){
                $f = true;
                break;
            }
        }
        if ($f && $curRoom[0] != 'monster'){
            $this->pos = $tar;
            $curRoom = explode(" ", $this->rooms[$this->pos]);
        }
        return $this->getRoomInfo();
    }
    public function fight(): array{
        $info = array();
        $info['location'] = $this->pos;
        $curRoom = explode(" ", $this->rooms[$this->pos]);
        $info['currentScore'] = $this->score;
        if ($curRoom[0] == 'monster'){
            $hp = intval($curRoom[1]);
            $damage = random_int(0, 100);
            if ($hp <= $damage){
                $this->score += $this->monsterHP[$this->pos];
                $this->monsterHP[$this->pos] = 0;
                $info['status'] = 'empty room';
                $info['availableMoves'] = $this->doors[$this->pos];
                $this->rooms[$this->pos] = 'empty';
            }
            else{
                $hp -= $damage;
                $info['ststus'] = "monster with {$hp}HP";
                $info['availableMoves'] = array();
                $this->rooms[$this->pos] = "monster {$hp}";
            }
            $info['currentScore'] = $this->score;
        }
        else{
            $info['status'] = 'empty room';
            $info['availableMoves'] = $this->doors[$this->pos];
        }
        if ($this->checkFinish())
            return $this->goToFinish();
        return $info;
    }
    private function goToFinish(): array{
        $info = array();
        $info['status'] = 'Dungeon Is Over';
        $info['finalScore'] = $this->score;

        $q = new Queue();
        $q->enqueue($this->start);

        $prev = array();
        $prev[$this->start] = -1;
        while (!$q->isEmpty()){
            $cur = $q->dequeue();
            foreach ($this->doors[$cur] as $room){
                if (!isset($prev[$room])){
                    $prev[$room] = $cur;
                    $q->enqueue($room);
                }
                if ($room == $this->finish)
                    break;
            }
            if (isset($prev[$this->finish]))
                break;
        }

        $path = array();
        $cur = $this->finish;
        while ($cur != -1){
            $path[] = $cur;
            $cur = $prev[$cur];
        }
        $path = array_reverse($path);
        $info['shortestRouteToExit'] = $path;
        return $info;
    }
}