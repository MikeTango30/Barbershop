<?php

namespace Barberhsop\Reservations\Database;

use Barbershop\Reservations\AvailableTimes;

class DbAvailableTimes
{
    public function insertTimes($reservationDate) {
        $query = 'INSERT INTO reservation WHERE ReservationDate = :ReservationDate';
        $sth = $this->db->prepare($query);
        $sth->execute(['$ReservationDate'=>$reservationDate]);
    }
}