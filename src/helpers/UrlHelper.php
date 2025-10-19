<?php
namespace app\helpers;

class UrlHelper {
    public static function baseUrl($path = '') {
        $basePath = '/caixaeletronico/public';
        $path = ltrim($path, '/');
        return $basePath . ($path ? '/' . $path : '');
    }
    
    public static function asset($path) {
        return self::baseUrl('assets/' . ltrim($path, '/'));
    }
}