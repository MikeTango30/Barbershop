<?php

namespace Barbershop\Models;

use Barbershop\Domain\Customer;
use Barbershop\Exceptions\NotFoundException;
use Barbershop\Exceptions\DbException;
use PDO;

class CustomerModel extends AbstractModel 
{
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
        //$lastId = $this->db->lastInsertId();
    }
}