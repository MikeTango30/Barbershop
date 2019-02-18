<?php

namespace Barbershop\Controllers;

use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\AvailableTimes;
use Barbershop\Domain\Reservation;
use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Reservations\SessionManager;
use Barbershop\Reservations\CustomerManager;
use Barbershop\Reservations\ReservationManager;
use \DateTime;

class ReservationController extends AbstractController 
{
    const PAGE_LENGTH = 10;
    
     //reservations for barber
    public function getReservedTimes($page): string {
        $page = (int)$page;
        $todayTomorrow = $this->getTodayTomorrow();
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getAll($page, self::PAGE_LENGTH);

        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "reservations" => $reservations,
            "currentPage" => $page,
            "lastPage" => count($reservations) < self::PAGE_LENGTH
        ];
        
        return $this->render("barberReservations.twig", $properties);
    }
    
    //reservations for barber - "1" is for $page variable in getReservedTimes($page) method to LIMIT clause in model
    public function getAllReservedTimes(): string {
        
        return $this->getReservedTimes(1);
    }

    //get reservations by date
    public function getByDate($page): string {
        $todayTomorrow = $this->getTodayTomorrow();
        
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getByDate($reservationDate);
        
        $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
            "reservations" => $reservations,
            "currentPage" => $page,
            "lastPage" => count($reservations) < self::PAGE_LENGTH
        ];
        
        return $this->render("barberReservations.twig", $properties);
    }
    
     public function getAllByDate(): string {
        
        return $this->getByDate(1);
    }
    
    //search for customer by name or surname
    public function search(): string {
        $firstname = $this->request->getParams()->getString("firstname");
    
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->search($firstname);
        $properties = [
            "reservations" => $reservations,
            "currentPage" => 1,
            "lastPage" => true
        ];
        
        return $this->render("barberReservations.twig", $properties);
    }
    
    //gets all available times
    public function availableTimes($page = 1) {
        $todayTomorrow = $this->getTodayTomorrow();
        
        $identity = $this->identifyUser();
        
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getDayAvailableTimes();
  
        
        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "availableTimes" => $availableTimes,
            "currentPage" => $page,
            "lastPage" => count($availableTimes) < self::PAGE_LENGTH,
            "pageLength" => self::PAGE_LENGTH,
            "urlParams" => $this->request->getParams()->getAllParametersAsArray()
        ];
        
        return $this->render($identity.".twig", $properties);
    }
    
    public function dayAvailableTime($page = 1) {
        $todayTomorrow = $this->getTodayTomorrow();
        $identity = $this->identifyUser();
        
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getDayAvailableTimes($reservationDate);
        var_dump(count($availableTimes));
        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "availableTimes" => $availableTimes,
            "currentPage" => $page,
            "pageLength" => self::PAGE_LENGTH,
            "reservationDate" => $reservationDate,
            "urlParams" => $this->request->getParams()->getAllParametersAsArray()
        ];
        
        return $this->render($identity."AvailableDay.twig", $properties);
    }
    
    //renders form of chosen time
    public function chooseTime() {
        $arrivalTime = $this->request->getParams()->getString("reservationDate");
        $identity = $this->identifyUser();
        
        return $this->render($identity."ReserveForm.twig", ["params"=>$arrivalTime]);
    }
    
    //starts session and reserves time
    public function reserveTime() {
        //gets all needed params
        $formParameters = [
            "arrival" => $this->request->getParams()->getString("reservationDate"),
            "firstname" => $this->request->getParams()->getString("firstname"),
            "surname" => $this->request->getParams()->getString("surname"),
            "phone" => $this->request->getParams()->getString("phone")
        ];
        var_dump($formParameters);
        
        $todayTomorrow = $this->getTodayTomorrow();
        $identity = $this->identifyUser();
        
        $sessionManager = new SessionManager();
        $sessionManager->startSession();
        $sessionManager->setSession("phone", $formParameters["phone"], $formParameters["arrival"]);
    
        $reservationManager = new ReservationManager($this->db);
        $errors = $reservationManager->manageReservation($formParameters);
        
        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "formParameters" => $formParameters,
            "errors"=> $errors
        ];
     
        if (!empty($errors) ) {
    
             return $this->render($identity."ReserveForm.twig", $properties);
        } else {
            
            return $this->render($identity."Reserved.twig", $properties); 
        }
    }
    
    //cancels time by deleting from db
    public function cancelTime() {
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $arrivalTime = $this->request->getParams()->getString("arrivalTime");

        $reservationModel = new ReservationModel($this->db);
        $reservationModel->cancelReservation($reservationDate, $arrivalTime);
        
        $identity = $this->identifyUser();
        
        if ($identity == "barber") {
            
            return $this->getAllReservedTimes();
        } else {
            
            return $this->getAllAvailableTimes();
        }    
    }
}