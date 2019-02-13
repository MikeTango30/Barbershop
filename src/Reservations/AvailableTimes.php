<?php

namespace Barbershop\Reservations;

use \DateTime;
use \DateInterval;
use \DatePeriod;

class AvailableTimes
{
    private $startTime;
    private $endTime;
    private $interval;
    private $period;
 
    public function __construct() {
        $this->startTime = new DateTime("10:00");
        $this->endTime = new DateTime("20:00");
        $this->interval = new DateInterval('PT15M');
        $this->period = new DatePeriod($this->startTime, $this->interval, $this->endTime);
        }
    
    private function modifyPeriod() {
        return $this->period = new DatePeriod(
            $this->startTime->modify("+1 days"), 
            $this->interval, 
            $this->endTime->modify("+1 days")
            );
    }
    
    public function getTodayTimes() {
        return $this->period;
    }
    
    public function getTimes($days) {
        for ($i = 0; $i < $days; $i++){
            $times [] = $this->modifyPeriod();
        }
        return $times;
    }
}