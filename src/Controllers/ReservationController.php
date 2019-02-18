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
        
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getAll($page, self::PAGE_LENGTH);

        $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
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
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
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
    public function availableTimes($page) {
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
        
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getAvailableTimes();
        
        $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
            "availableTimes" => $availableTimes,
            "currentPage" => $page,
            "lastPage" => count($availableTimes) < self::PAGE_LENGTH,
            "pageLength" => self::PAGE_LENGTH
        ];
        
        return $this->render($identity."AvailableDay.twig", $properties);
    }
    
    public function getAllAvailableTimes(): string {
        
        return $this->availableTimes(1);
    }
    
    public function dayAvailableTime($page) {
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
        
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getDayAvailableTimes($reservationDate);
        
        $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
            "availableTimes" => $availableTimes,
            "currentPage" => $page,
            "lastPage" => count($availableTimes) < self::PAGE_LENGTH,
            "pageLength" => self::PAGE_LENGTH,
            "reservationDate" => $reservationDate
        ];
        
        return $this->render($identity."AvailableDay.twig", $properties);
    }
    
    public function getAllDayAvailableTimes(): string {
        
        return $this->dayAvailableTime(1);
    }
    
    //renders form of chosen time
    public function chooseTime() {
        $arrivalTime = $this->request->getParams()->getString("reservationDate");
         
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
        
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
        
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        $sessionManager = new SessionManager();
        $sessionManager->startSession();
        $sessionManager->setSession("phone", $formParameters["phone"], $formParameters["arrival"]);
    
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
      
        $errors = $reservationManager->manageReservation($formParameters);
        $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
            "formParameters" => $formParameters,
            "errors"=> $errors
        ];
      
        if (isset($errors) ) {
             
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
        
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
        
        if ($identity == "barber") {
            
            return $this->getAllReservedTimes();
        } else {
            
            return $this->getAllAvailableTimes();
        }    
    }
}