<?php
namespace app\core;
class Logger {
    public static function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents('../logs/transactions.log', $logMessage, FILE_APPEND);
    }
}