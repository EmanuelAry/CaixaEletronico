<?php
namespace app\core;

use app\contracts\core\INotification;

class Notification implements INotification {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
    }

    public function add($message, $type = 'success') {
        $_SESSION['notifications'][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function getNotifications() {
        $notifications = $_SESSION['notifications'] ?? [];
        $_SESSION['notifications'] = [];
        return $notifications;
    }
}