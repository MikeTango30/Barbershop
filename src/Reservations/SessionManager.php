<?php

namespace Barbershop\Reservations;

class SessionManager
{
    private static $sessionStarted = false;
    
    //starts session if not started
    public static function startSession() {
        
        if (self::$sessionStarted == false) {
            session_start();
            self::$sessionStarted = true;
        }
    }
    
    public static function destroySession() {
        if (self::$sessionStarted == true) {
            session_destroy();
            self::$sessionStarted = false;
        }
    }
    
    //sets session key and value
    public static function setSession($key, $value, $request) {
        $_SESSION[$key] = $value;
        setcookie($key, $value);
        return $cookie = $request->getCookies()->get($key);
    }
    
    //gets session key and value
    public static function getSession($key) {
        if(isset($_SESSION[$key])) {
            
            return $_SESSION[$key];
        }
        else {
            
            return false;
        }
    }
}