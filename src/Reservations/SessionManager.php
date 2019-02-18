<?php

namespace Barbershop\Reservations;

class SessionManager
{
    private $sessionStarted = false;
    
    //starts session if not started
    public function startSession() {
        if ($this->sessionStarted == false) {
            session_start();
            $this->sessionStarted = true;
        }
    }
    
    public function destroySession() {
        if ($this->sessionStarted == true) {
            session_destroy();
            $this->sessionStarted = false;
        }
    }
    
    //sets session key and value
    public function setSession($key, $value,$arrival, $request) {
        $_SESSION[$key] = $value;
        setcookie($key, $value, ((new \DateTime)->modify($arrival)->getTimestamp()), "/");
    }
    
    //gets session key and value
    public function getSession($key) {
        if(isset($_SESSION[$key])) {
            
            return $_SESSION[$key];
        }
        else {
            
            return false;
        }
    }
}