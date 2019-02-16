<?php
namespace Barbershop\Controllers;

use Barbershop\Domain\Customer;
use Barbershop\Models\CustomerModel;
use Barbershop\Controllers\ErrorController;
use Barbershop\Reservations\SessionManager;

class CustomerController extends AbstractController 
{
    
    public function idCustomer($firstname, $surname, $phone) {
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
    
    public function getCustomerReservation() {
        SessionManager::startSession();
        var_dump($_SESSION["phone"]);
        var_dump($_COOKIE);
        var_dump($this->cookie);
        
        
        if (empty($_SESSION["phone"])) {
            $reservationController = new ReservationController($this->di, $this->request);
            return $reservationController->availableTimes();
        }
        
        else {
            $customerModel = new CustomerModel($this->db);
            $customer = $customerModel->getCustomer("phone");
            $reservation = $customerModel->getReservation($customer);
            
            return $this->render("myReservation.twig", ["params"=>$reservation]);
        }    
    }
}         