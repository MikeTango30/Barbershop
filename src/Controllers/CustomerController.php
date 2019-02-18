<?php
namespace Barbershop\Controllers;

use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Controllers\ErrorController;
use Barbershop\Reservations\SessionManager;
use Barbershop\Reservations\AvailableTimes;

class CustomerController extends AbstractController 
{
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