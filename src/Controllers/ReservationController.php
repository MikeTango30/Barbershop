<?php

namespace Barbershop\Controllers;

use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\AvailableTimes;

class ReservationController extends AbstractController 
{
    const PAGE_LENGTH = 10;
    
    public function getAllWithPage($page): string {
        $page = (int)$page;
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getAll($page, self::PAGE_LENGTH);
        $properties = [
            'reservations' => $reservations,
            'currentPage' => $page,
            'lastPage' => count($reservations) < self::PAGE_LENGTH
        ];
        return $this->render('reservations.twig', $properties);
    }

    public function getAll(): string {
        return $this->getAllWithPage(1);
    }

    public function getByDate(): string {
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->getByDate($resDate);
        $properties = [
            'reservations' => $reservations,
            'currentPage' => 1,
            'lastPage' => true
        ];
        return $this->render('reservations.twig', $properties);
    }
    
    public function search(): string {
        $firstname = $this->request->getParams()->getString('firstname');
    
        $reservationModel = new ReservationModel($this->db);
        $reservations = $reservationModel->search($firstname);
        $properties = [
            'reservations' => $reservations,
            'currentPage' => 1,
            'lastPage' => true
        ];
        return $this->render('reservations.twig', $properties);
    }
    
    //
    public function availableTimes() {
        $times = new AvailableTimes($this->db);
        $availableTimes = $times->getAvailableTimes();
        
        return $this->render('customer.twig', ["params"=>$availableTimes]);
    }
}