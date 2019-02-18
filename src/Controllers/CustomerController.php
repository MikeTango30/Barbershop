<?php
namespace Barbershop\Controllers;

use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Controllers\ErrorController;
use Barbershop\Reservations\SessionManager;
use Barbershop\Reservations\AvailableTimes;
use \DateTime;

class CustomerController extends AbstractController 
{
    const PAGE_LENGTH = 10;
    
    //show customer reservation if any or available times
    public function getCustomerReservation($page) {
        $page = (int) $page;
        
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        $sessionManager = new SessionManager();
        $sessionManager->startSession();
        
        if (!isset($_COOKIE["phone"])) {
            $times = new AvailableTimes($this->db);
            $availableTimes = $times->getAvailableTimes($page);
            
            $properties = [
            "today" => $today->format("Y-m-d"),
            "tomorrow" => $tomorrow->format("Y-m-d"),
            "availableTimes" => $availableTimes,
            "currentPage" => 1,
            "lastPage" => count($availableTimes) < self::PAGE_LENGTH,
            "pageLength" => self::PAGE_LENGTH
        ];
            
            return $this->render("customer.twig", $properties);
        }
        
        else {
            $customerModel = new CustomerModel($this->db);
            $customer = $customerModel->getCustomer();
            var_dump($customer);
            $reservation = $customerModel->getReservation($customer);
            
            return $this->render("myReservation.twig", ["params"=>$reservation]);
        }  
    }
    public function getMyReservation(): string {
        
        return $this->getCustomerReservation(1);
    }
}         