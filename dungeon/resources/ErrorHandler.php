<?php

class ErrorHandler{
    public static function handleException(Throwable $e){
        http_response_code(500);

        echo json_encode([
            "code" => $e->getCode(),
            "message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ]);
    }
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ){
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}