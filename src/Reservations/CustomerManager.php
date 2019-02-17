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
    
    public function createCustomer($firstname, $surname, $phone) {
        $customer = new Customer();
        $customer->setFirstname($firstname);
        $customer->setSurname($surname);
        $customer->setPhone($phone);
        
        $customerModel = new CustomerModel($this->db);
            if ($customerModel->isInserted($phone)) {
                
                return $customerId = $customerModel->getId($phone);
            }
            else {
                $customerModel->insertCustomer($customer);
                
                return $customerId = $this->db->lastInsertId();
            }
    }
}

//     public function saveCustomer($phone, Customer $customer) {
//         $customerModel = new CustomerModel($this->db);
//             if ($customerModel->isInserted($phone)) {
//                 return $customerId = $customerModel->getId($phone);
//             }
//             else {
//                 $customerModel->insertCustomer($customer);
//                 return $customerId = $this->db->lastInsertId();
//             }
//     }
// }    