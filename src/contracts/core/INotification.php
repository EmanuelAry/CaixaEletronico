<?php

namespace app\contracts\core;

interface INotification {
    public function add($message, $type = 'info');
    public function getNotifications();
}
