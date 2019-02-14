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
    
    public function getAll(int $page, int $pageLength): array {
        $start = $pageLength * ($page - 1);
        
        $query = 'SELECT * FROM reservation LIMIT :page, :length';
        $sth = $this->db->prepare($query);
        $sth->bindParam('page', $start, PDO::PARAM_INT);
        $sth->bindParam('length', $pageLength, PDO::PARAM_INT);
        $sth->execute();
    
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
    }
    
    //
    public function loadReservations() {
        $query = 'SELECT * FROM reservation';
        $sth = $this->db->prepare($query);
        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);

    }    
    
    public function getByName($firstname) {
        //TODO
    }
    
    public function getByTimes($timesBeen) {
        //TODO
    }
    
    public function makeReservation(Reservation $reservation) {
        $query = 'INSERT INTO reservation (customer_id, reservationDate, arrivalTime) VALUES(:customer_id, :reservationDate, :arrivalTime)';
        $sth = $this->db->prepare($query);
        $sth->bindParam('customer_id', $this->customer_id, PDO::PARAM_STR);
        $sth->bindParam('reservationDate', $this->reservationDate, PDO::PARAM_STR);
        $sth->bindParam('arrivalTime', $this->arrivalTime, PDO::PARAM_STR);
        $sth->execute();
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