<?php

namespace Barbershop\Controllers;

use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\AvailableTimes;
use Barbershop\Domain\Reservation;
use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Controllers\ErrorController;

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
        $reservations = $reservationModel->getByDate($resDate);
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
    
    //reserves time by inserting customer and reservations into Db, checks if form fields are filled
    public function reserveTime() {
        $arrivalTime    = $this->request->getParams()->getString("reservationDate");
        $firstname      = $this->request->getParams()->getString("firstname");
        $surname        = $this->request->getParams()->getString("surname");
        $phone          = $this->request->getParams()->getString("phone");
        
        $check = new ReservationValidator();
        if ($check->isValid($firstname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        elseif ($check->isValid($surname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        elseif ($check->isValid($phone == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
       
        $customer = new Customer($firstname, $surname, $phone);
        $customerModel = new CustomerModel($this->db);
        $customerModel->insertCustomer($customer);
        $lastId = $this->db->lastInsertId();
        
        $reservation = new Reservation();
        $reservation->setCustomerId($lastId);
        $reservation->setArrivalTime(date("H:i:s", strtotime($arrivalTime)));
        $reservation->setReservationDate(date("Y-m-d", strtotime($arrivalTime)));
      
        $reservationModel = new ReservationModel($this->db);
        $reservationModel->createReservation($reservation);
        
        return $this->render("reserved.twig", ["params"=>$arrivalTime]);
    }
}