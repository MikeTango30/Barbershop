<?php

namespace Barbershop\Models;

use Barbershop\Domain\Reservation;
use Barbershop\Exceptions\DbException;
use Barbershop\Exceptions\NotFoundException;
use Barbershop\Models\AbstractModel;
use Barbershop\Reservations\SessionManager;
use Barbershop\Controllers\ErrorController;

use PDO;

class ReservationModel extends AbstractModel
{
    //const PAGE_LENGTH = 3;
    const CLASSNAME = "Barbershop\Domain\Reservation";
    
    //todo
    public function getByDate($reservationDate) {
        $query = "SELECT *, count(customer.id) FROM reservation 
            JOIN customer ON customer.id= reservation.customer_id 
            WHERE DATE (reservationDate) = DATE(:reservationDate) 
            GROUP BY arrivalTime";
        $sth = $this->db->prepare($query);
        $sth->bindParam("reservationDate", $reservationDate, PDO::PARAM_STR);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    //get reservations for two weeks, 10 per page
    public function getAll(int $page, int $pageLength): array {
        $start = $pageLength * ($page - 1);
        
        $query = "SELECT *, count(customer.id) FROM reservation 
            JOIN customer ON customer.id= reservation.customer_id 
            WHERE DATE (reservationDate) > TIME(NOW()) AND DATE (reservationDate) < DATE(NOW() + INTERVAL 14 DAY) 
            GROUP BY arrivalTime LIMIT :page, :length";
        $sth = $this->db->prepare($query);
        $sth->bindParam("page", $start, PDO::PARAM_INT);
        $sth->bindParam("length", $pageLength, PDO::PARAM_INT);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    //load all reservations
    public function loadReservations() {
        $query = "SELECT * FROM reservation";
        $sth = $this->db->prepare($query);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

    }
    
    //search by name or surname
    public function search($firstname) {
         $query = "SELECT *, count(customer.id) FROM reservation 
            JOIN customer ON customer.id= reservation.customer_id 
            WHERE firstname LIKE :firstname OR surname LIKE :surname 
            GROUP BY arrivalTime";
        $sth = $this->db->prepare($query);
        $sth->bindValue("firstname", "%$firstname%");
        $sth->bindValue("surname", "%$firstname%");
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    //sort by times been
    public function getSortedByTimesBeen(int $page, int $pageLength): array {
        $start = $pageLength * ($page - 1);
        
        $query = "SELECT *, count(customer.id) FROM reservation
            JOIN customer ON customer.id= reservation.customer_id
            GROUP BY customer.id ORDER BY count(customer.id) DESC
            LIMIT :page, :length";
            
            
    //   $query = "SELECT *, count(customer.id) as reservationCount FROM reservation
    //         JOIN customer ON customer.id= reservation.customer_id
    //         GROUP BY customer.id ORDER BY count(customer.id) DESC
    //         LIMIT :page, :length";
        $sth = $this->db->prepare($query);
        $sth->bindParam("page", $start, PDO::PARAM_INT);
        $sth->bindParam("length", $pageLength, PDO::PARAM_INT);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    
    //  //load all reservations
    // public function doesReservationExist($customerId, $date) {
    //     $query = "SELECT * FROM reservation WHERE customer_id = :customerId";
    //     $sth = $this->db->prepare($query);
    //     $sth->bindParam("customer_id", $customeId, PDO::PARAM_STR);
    //     $sth->execute();
        
    //     return !empty($sth->fetchAll ()) ? true : false;
    // }    
    
    
    //insert reservation into Db
    public function createReservation(Reservation $reservation) {
    
        $query = "INSERT INTO reservation (customer_id, reservationDate, arrivalTime) VALUES(:customer_id, :reservationDate, :arrivalTime)";
        $customerId = $reservation->getCustomerId();
        $reservationDate = $reservation->getReservationDate();
        $arrivalTime = $reservation->getArrivalTime();
        
        $sth = $this->db->prepare($query);
        $sth->bindParam("customer_id", $customerId, PDO::PARAM_STR);
        $sth->bindParam("reservationDate", $reservationDate, PDO::PARAM_STR);
        $sth->bindParam("arrivalTime", $arrivalTime, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
    }
    
    //delete reservation from Db
    public function cancelReservation($reservationDate, $arrivalTime) {
        $query = "DELETE FROM reservation WHERE reservationDate=:reservationDate AND arrivalTime=:arrivalTime";
        $sth = $this->db->prepare($query);
        $sth->bindParam("reservationDate", $reservationDate, PDO::PARAM_STR);
        $sth->bindParam("arrivalTime", $arrivalTime, PDO::PARAM_STR);
        $sth->execute();
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        setcookie("phone", "", time()-3600);
    }
    
}