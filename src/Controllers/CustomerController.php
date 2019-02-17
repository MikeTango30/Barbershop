<?php
namespace Barbershop\Controllers;

use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Controllers\ErrorController;
use Barbershop\Reservations\SessionManager;
use Barbershop\Reservations\AvailableTimes;

class CustomerController extends AbstractController 
{
    //insert and get id or id if already in db - needs to be seperated
    //public function idCustomer($firstname, $surname, $phone) {
        // čia reikia sukurti naują klasę, kurioje bus kuriamas Customer Objektas. TOje klasėje išsaugoti vartotoją. 
        // Taip galėsi išsikviesti tą pačią klasę rezervacijose 
        // new CustomerManager ir ta klasė turės metodą create customer.
        // taip pat kaip su available times darėm
        
        
        // $customer = new Customer();
        // $customer->setFirstname($firstname);
        // $customer->setSurname($surname);
        // $customer->setPhone($phone);
        
        // $customerModel = new CustomerModel($this->db);
        // if ($customerModel->isInserted($phone)) {
        //     return $customerId = $customerModel->getId($phone);
        // }
        // else {
        //     $customerModel->insertCustomer($customer);
        //     return $customerId = $this->db->lastInsertId();
        // }
    //}
    
    //show customer reservation if any or available times
    public function getCustomerReservation() {
        $sessionManager = new SessionManager();
        $sessionManager->startSession();
        
        if (!isset($_COOKIE["phone"])) {
            $times = new AvailableTimes($this->db);
            $availableTimes = $times->getAvailableTimes();
            
            return $this->render("customer.twig", ["params"=>$availableTimes]);
        }
        
        else {
            $customerModel = new CustomerModel($this->db);
            $customer = $customerModel->getCustomer("phone");
            $reservation = $customerModel->getReservation($customer);
            
            return $this->render("myReservation.twig", ["params"=>$reservation]);
        }  
        
    }
}         