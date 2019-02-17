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

class ReservationController extends AbstractController 
{
    const PAGE_LENGTH = 10;
    
    public function getAllWithPage($page): string {
        $page = (int)$page;
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getAll($page, self::PAGE_LENGTH);
        $properties = [
            "reservations" => $reservations,
            "currentPage" => $page,
            "lastPage" => count($reservations) < self::PAGE_LENGTH
        ];
        return $this->render("reservations.twig", $properties);
    }

    public function getAll(): string {
        return $this->getAllWithPage(1);
    }

    public function getByDate(): string {
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getByDate($reservationDate);
        $properties = [
            "reservations" => $reservations,
            "currentPage" => 1,
            "lastPage" => true
        ];
        return $this->render("reservations.twig", $properties);
    }
    
    public function search(): string {
        $firstname = $this->request->getParams()->getString("firstname");
    
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->search($firstname);
        $properties = [
            "reservations" => $reservations,
            "currentPage" => 1,
            "lastPage" => true
        ];
        
        return $this->render("reservations.twig", $properties);
    }
    
    //gets all available times
    public function availableTimes() {
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getAvailableTimes();
     
        return $this->render("customer.twig", ["params"=>$availableTimes]);
    }
    
    //renders form of chosen time
    public function chooseTime() {
        $arrivalTime = $this->request->getParams()->getString("reservationDate");
        
        return $this->render("customerReserveForm.twig", ["params"=>$arrivalTime]);
    }
    
    //starts session and reserves time
    public function reserveTime() {
        //gets all needed params
        $arrival = $this->request->getParams()->getString("reservationDate");
        $firstname = $this->request->getParams()->getString("firstname");
        $surname = $this->request->getParams()->getString("surname");
        $phone = $this->request->getParams()->getString("phone");
        
        $sessionManager = new SessionManager();
        $sessionManager->startSession();
        $sessionManager->setSession("phone", $phone, $arrival, $this->request);
      
        $reservationManager = new ReservationManager($this->db);
        $reservationManager->manageReservation($firstname, $surname, $phone, $arrival);
        
        return $this->render("reserved.twig", ["params"=>$arrival]);
    }
    
    //cancels time by deleting from db
    public function cancelTime() {
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $arrivalTime = $this->request->getParams()->getString("arrivalTime");

        $reservationModel = new ReservationModel($this->db);
        $reservationModel->cancelReservation($reservationDate, $arrivalTime);
        
        return $this->availableTimes();
    }
}