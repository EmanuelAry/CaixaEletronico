<?php
namespace app\core;
class Notification {
    public static function add($message, $type = 'info') {
        $_SESSION['notifications'][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public static function show() {
        if(isset($_SESSION['notifications'])) {
            foreach($_SESSION['notifications'] as $notification) {
                echo "<div class='alert alert-{$notification['type']}'>{$notification['message']}</div>";
            }
            unset($_SESSION['notifications']);
        }
    }
}