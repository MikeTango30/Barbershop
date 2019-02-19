<?php 

namespace Barbershop\Reservations;

use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;


class CustomerManager
{
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createCustomer($formParameters) {
        $customer = new Customer();
        $customer->setFirstname($formParameters["firstname"]);
        $customer->setSurname($formParameters["surname"]);
        $customer->setPhone($formParameters["phone"]);
        
        $customerModel = new CustomerModel($this->db);
        if ($customerModel->isInserted($formParameters["phone"])) {
            $customerId = $customerModel->getId($formParameters["phone"]);
        } else {
            $customerModel->insertCustomer($customer);
            $customerId = $this->db->lastInsertId();
        }
        
        return $customerId;
    }
}