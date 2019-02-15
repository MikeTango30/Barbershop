<?php

namespace Barbershop\Reservations;

use \DateTime;
use \DateInterval;
use \DatePeriod;
use Barbershop\Models\ReservationModel;



class AvailableTimes
{
    private $startTime;
    private $endTime;
    private $interval;
    private $period;
    private $db;
 
    public function __construct($db) {
        $this->startTime = new DateTime("10:00");
        $this->endTime = new DateTime("20:00");
        $this->interval = new DateInterval('PT15M');
        $this->period = new DatePeriod($this->startTime, $this->interval, $this->endTime);
        $this->db = $db;
    }
    
    private function modifyPeriod() {
        return $this->period = new DatePeriod(
            $this->startTime->modify("+1 days"), 
            $this->interval, 
            $this->endTime->modify("+1 days")
            );
    }
    
    public function getTimes($days) {
        for ($i = 0; $i < $days; $i++){
            
            $times[] = $this->modifyPeriod();
        }
        foreach($times as $day) {
            foreach($day as $dt) {
                $timesStr [$dt->format('Y-m-d H:i:s')] = $dt->format('Y-m-d H:i:s');
            }
        }    
        return $timesStr;
    }
    
    public function getAvailableTimes() {
        $times = $this->getTimes(1);
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->loadReservations();
        
        foreach($reservations as $reservation) {
            $reservationsStr[
                $reservation->getReservationDate()." ".
                $reservation->getArrivalTime()
                ] =
                    $reservation->getReservationDate()." ".
                    $reservation->getArrivalTime();
            //unset($times[$reservation->getReservationDate()." ".$reservation->getArrivalTime()]);
        }
        $availableTimes = array_diff($times, $reservationsStr);
        
        return $availableTimes;
        // var_dump($times);
        // var_dump($reservationsStr);
    }
}