<?php

namespace Barbershop\Models;

use Barbershop\Domain\Customer;
use Barbershop\Domain\Customer\CustomerFactory;
use Barbershop\Exceptions\NotFoundException;

class CustomerModel extends AbstractModel 
{
    public function insertCustomer(Customer $customer) {
        $query = 'INSERT INTO customer (firstname, surname, phone) VALUES(:firstname, :surname, :phone)';
        $sth = $this->db->prepare($query);
        $sth->bindParam('firstname', $this->firstname, PDO::PARAM_STR);
        $sth->bindParam('surname', $this->surname, PDO::PARAM_STR);
        $sth->bindParam('phone', $this->phone, PDO::PARAM_STR);
        $sth->execute();
    }
}