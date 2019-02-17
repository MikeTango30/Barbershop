<?php

namespace Barbershop\Models;

use Barbershop\Domain\Customer;
use Barbershop\Domain\Reservation;
use Barbershop\Exceptions\NotFoundException;
use Barbershop\Exceptions\DbException;
use Barbershop\Reservations\SessionManager;
use Barbershop\Models\AbstractModel;
use PDO;

class CustomerModel extends AbstractModel 
{
    const CLASSNAME = 'Barbershop\Domain\Customer';

    
    //inserts new customer
    public function insertCustomer(Customer $customer) {
        $query = "INSERT INTO customer (firstname, surname, phone) VALUES(:firstname, :surname, :phone)";
        $firstname = $customer->getFirstname();
        $surname = $customer->getSurname();
        $phone = $customer->getPhone();
        
        $sth = $this->db->prepare($query);
        $sth->bindParam("firstname", $firstname, PDO::PARAM_STR);
        $sth->bindParam("surname", $surname, PDO::PARAM_STR);
        $sth->bindParam("phone", $phone, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
    }
    
    //check if customer is already in Db
    public function isInserted($phone): bool {
        $query = "SELECT * FROM customer WHERE phone=:phone";
        $sessionValue = SessionManager::getSession("phone");
        
        $sth = $this->db->prepare($query);
        $sth->bindParam("phone", $sessionValue, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        
        return !empty($sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME)) ? true : false;
    }
    
    public function getId($phone) {
        $query = "SELECT id FROM customer WHERE phone=:phone";
        $sessionValue = SessionManager::getSession("phone");
        $sth = $this->db->prepare($query);
        $sth->bindParam("phone", $sessionValue, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        
        $customerId = $sth->fetchAll(PDO::FETCH_COLUMN);
        return $customerId[0];
        
    }
    
    //gets customer object from Db
    public function getCustomer(): Customer {
        $query = "SELECT * FROM customer WHERE phone=:phone";
        
        $sth = $this->db->prepare($query);
        $sth->bindParam("phone", $_COOKIE["phone"], PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        $customer = $sth->fetchAll(PDO::FETCH_CLASS, self::CLASSNAME);
        
        return $customer[0];
    }
    
    //gets reservation by customer
    public function getReservation(Customer $customer) {
        $query = "SELECT * FROM reservation WHERE customer_id=:id";
        
        $customerId = $customer->getId();
        $sth = $this->db->prepare($query);
        $sth->bindParam("id", $customerId, PDO::PARAM_STR);
        
        if (!$sth->execute()) {
            throw new DbException($sth->errorInfo()[2]);
        }
        $reservation = $sth->fetchAll(PDO::FETCH_CLASS, Reservation::class);
        
        return $reservation[0];
    }
}