<?php

namespace Barbershop\Models;

use Barbershop\Domain\Reservation;
use Barbershop\Exceptions\DbException;
use Barbershop\Exceptions\NotFoundException;
use Barbershop\Models\AbstractModel;
use Barbershop\Reservations\SessionManager;

use PDO;

class ReservationModel extends AbstractModel
{
    const CLASSNAME = 'Barbershop\Domain\Reservation';
    
    //todo
    public function getByDate($reservationDate) {
        $query = 'SELECT * FROM reservation WHERE reservationDate = :reservationDate';
        $sth = $this->db->prepare($query);
        $sth->bindParam('reservationDate', $reservationDate, PDO::PARAM_STR);
        $sth->execute();
        
        $reservations = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        
        if (empty($reservations)) {
            throw new NotFounException();
        }
        
        return $reservations;
    }
    
    //todo
    public function getAll(int $page, int $pageLength): array {
        $start = $pageLength * ($page - 1);
        
        $query = 'SELECT * FROM reservation LIMIT :page, :length';
        $sth = $this->db->prepare($query);
        $sth->bindParam('page', $start, PDO::PARAM_INT);
        $sth->bindParam('length', $pageLength, PDO::PARAM_INT);
        $sth->execute();
    
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    //load all reservations
    public function loadReservations() {
        $query = 'SELECT * FROM reservation';
        $sth = $this->db->prepare($query);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

    }    
    
    public function getByTimes($timesBeen) {
        //TODO  
    }
    
    //insert reservation into Db
    public function createReservation(Reservation $reservation) {
        $query = 'INSERT INTO reservation (customer_id, reservationDate, arrivalTime) VALUES(:customer_id, :reservationDate, :arrivalTime)';
        $customerId = $reservation->getCustomerId();
        $reservationDate = $reservation->getReservationDate();
        $arrivalTime = $reservation->getArrivalTime();
        
        $sth = $this->db->prepare($query);
        $sth->bindParam('customer_id', $customerId, PDO::PARAM_STR);
        $sth->bindParam('reservationDate', $reservationDate, PDO::PARAM_STR);
        $sth->bindParam('arrivalTime', $arrivalTime, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
    }
    
    //delete reservation from Db
    public function cancelReservation($reservationDate, $arrivalTime) {
        $query = 'DELETE FROM reservation WHERE reservationDate=:reservationDate AND arrivalTime=:arrivalTime';
        $sth = $this->db->prepare($query);
        $sth->bindParam('reservationDate', $reservationDate, PDO::PARAM_STR);
        $sth->bindParam('arrivalTime', $arrivalTime, PDO::PARAM_STR);
        $sth->execute();
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        setcookie("phone", "", time()-3600);
        var_dump($_COOKIE["phone"]);
        SessionManager::destroySession();
    }
    
    public function search() {
        //TODO
    }
}