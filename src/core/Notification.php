<?php
namespace app\core;
use app\contracts\core\INotification;
class Notification implements INotification {
    public function add($message, $type = 'info') {
        $_SESSION['notifications'][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function show() {
        if(isset($_SESSION['notifications'])) {
            foreach($_SESSION['notifications'] as $notification) {
                echo "<div class='alert alert-{$notification['type']}'>{$notification['message']}</div>";
            }
            unset($_SESSION['notifications']);
        }
    }
}