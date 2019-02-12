<?php

namespace Barbershop\Domain;

class Customer {
    
    private $firstname;
    private $surname;
    private $phone;
    
    
    public function __construct(
        string $firstname, 
        string $surname,
        string $phone
    ) {
        $this->firstname = $firstname;
        $this->surname = $surname;
        $this->phone = $phone;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getSurname(): string {
        return $this->surname;
    }
    
    public function getPhone(): string {
        return $this->phone;
    }
    
    public function setPhone(string $phone) {
        $this->phone = $phone;
    }
}