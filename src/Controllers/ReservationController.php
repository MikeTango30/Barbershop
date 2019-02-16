<?php

namespace Barbershop\Controllers;

use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\AvailableTimes;
use Barbershop\Domain\Reservation;
use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Reservations\SessionManager;

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
    
    
    /**
    *reserves time by inserting customer and reservations into Db, checks if form fields are filled
    *needs to be restructured
    **/
    public function reserveTime() {
        //gets all needed params
        $arrival = $this->request->getParams()->getString("reservationDate");
        $firstname = $this->request->getParams()->getString("firstname");
        $surname = $this->request->getParams()->getString("surname");
        $phone = $this->request->getParams()->getString("phone");

        //check if form is filled
        $check = new ReservationValidator();
        $check->checkFieldsValidity($firstname, $surname, $phone);
        
        SessionManager::startSession();
        SessionManager::setSession("phone", $phone, $arrival, $this->request);
      
        

        $customerController = new CustomerController($this->di, $this->request);
        $customerId = $customerController->idCustomer($firstname, $surname, $phone);
        
         //insert reservation
        $reservation = new Reservation();
        $reservation->setCustomerId($customerId);
        $reservation->setArrivalTime(date("H:i:s", strtotime($arrival)));
        $reservation->setReservationDate(date("Y-m-d", strtotime($arrival)));
        
        $reservationModel = new ReservationModel($this->db);
        $reservationModel->createReservation($reservation);
        
        return $this->render("reserved.twig", ["params"=>$arrival]);
    }
    
    public function cancelTime() {
        $reservationDate = $this->request->getParams()->getString("reservationDate");
        $arrivalTime = $this->request->getParams()->getString("arrivalTime");

        $reservationModel = new ReservationModel($this->db);
        $reservationModel->cancelReservation($reservationDate, $arrivalTime);
        
        //maybe router->route()
        return $this->availableTimes();
    }
}