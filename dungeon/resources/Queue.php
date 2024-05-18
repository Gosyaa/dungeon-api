<?php

class Queue {
    private $queue;

    public function __construct() {
        $this->queue = [];
    }

    public function enqueue($item) {
        array_push($this->queue, $item);
    }

    public function dequeue() {
        if (!$this->isEmpty()) {
            return array_shift($this->queue);
        }
        return null;
    }

    public function peek() {
        if (!$this->isEmpty()) {
            return $this->queue[0];
        }
        return null;
    }

    public function isEmpty() {
        return empty($this->queue);
    }
}