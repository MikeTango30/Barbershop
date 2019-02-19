<?php

namespace Barbershop\Controllers;

use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\AvailableTimes;
use Barbershop\Domain\Reservation;
// use Barbershop\Domain\Customer;
// use Barbershop\Models\CustomerModel;
// use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Reservations\SessionManager;
// use Barbershop\Reservations\CustomerManager;
use Barbershop\Reservations\ReservationManager;
use \DateTime;

class ReservationController extends AbstractController 
{
    const PAGE_LENGTH = 10;
    
     //reservations for barber
    public function getReservedTimes($page = 1): string {
        $page = (int)$page;
        $todayTomorrow = $this->getTodayTomorrow();
        
        $sorted = $this->request->getParams()->has("sort");
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getAll($page, self::PAGE_LENGTH, $sorted);

        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "reservations" => $reservations,
            "currentPage" => $page,
            "lastPage" => count($reservations) < self::PAGE_LENGTH,
            "urlParams" => $this->request->getParams()->getAllParametersAsArray()
        ];
        
        return $this->render("barberReservations.twig", $properties);
    }

    //get reservations by date
    public function getByDate($page = 1) {
        $todayTomorrow = $this->getTodayTomorrow();
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $sorted = $this->request->getParams()->has("sort");
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getByDate($reservationDate, $page, self::PAGE_LENGTH, $sorted);
        
        $properties = [
            "today" => $todayTomorrow["today"]->format("Y-m-d"),
            "tomorrow" => $todayTomorrow["tomorrow"]->format("Y-m-d"),
            "reservations" => $reservations,
            "currentPage" => $page,
            "lastPage" => count($reservations) < self::PAGE_LENGTH,
            "urlParams" => $this->request->getParams()->getAllParametersAsArray()
        ];
        
        return $this->render("barberReservations.twig", $properties);
    }
    
    //search for customer by name or surname
    public function search(): string {
        $firstname = $this->request->getParams()->getString("firstname");
        $sorted = $this->request->getParams()->has("sort");    
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->search($firstname, $sorted);
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
        var_dump($identity);
        
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        var_dump($reservationDate);
        
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getDayAvailableTimes($reservationDate);
        
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
            
            return $this->getReservedTimes();
        } else {
            
            return $this->getAvailableTimes();
        }    
    }
}