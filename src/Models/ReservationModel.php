<?php

namespace Barbershop\Models;

use Barbershop\Domain\Reservation;
use Barbershop\Exceptions\DbException;
use Barbershop\Exceptions\NotFoundException;
use Barbershop\Models\AbstractModel;
use PDO;

class ReservationModel extends AbstractModel
{
    const CLASSNAME = 'Barbershop\Domain\Reservation';
    
    public function getByDate(int $reservationDate) {
        $query = 'SELECT * FROM reservation WHERE ReservationDate = :ReservationDate';
        $sth = $this->db->prepare($query);
        $sth->execute(['ReservationDate'=>$reservationDate]);
        
        $reservations = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        
        if (empty($reservations)) {
            throw new NotFounException();
        }
        
        return $reservations;
    }
    
    public function getAll(int $page, int $pageLength): array {
        $start = $pageLength * ($page - 1);
        
        $query = 'SELECT * FROM reservation LIMIT :page, :length';
        $sth = $this->db->prepare($query);
        $sth->bindParam('page', $start, PDO::PARAM_INT);
        $sth->bindParam('length', $pageLength, PDO::PARAM_INT);
        $sth->execute();
    
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    public function checkAvailability($reservationDate, $arrivalTime) {
        $query = 'SELECT * FROM reservation WHERE ReservationDate = :ReservationDate AND ArrivalTime = :ArrivalTime';
        $sth = $this->db->prepare($query);
        $sth->execute(['ReservationDate'=>$reservationDate, 'ArrivalTime'=>$arrivalTime]);
        
        $reservations = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        
        if (empty($reservations)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }    
    
    public function getByName($firstname) {
        //TODO
    }
    
    public function getByTimes($timesBeen) {
        //TODO
    }
    
    public function makeReservation(): Reservation {
        //TODO
    }
    
    public function cancelReservation() {
        //TODO
    }
    
    public function isReserved(): bool {
        //TODO
    }
    
    public function search() {
        //TODO
    }
}