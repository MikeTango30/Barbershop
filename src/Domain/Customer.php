<?php

namespace Barbershop\Domain;

class Customer {
    
    private $firstname;
    private $surname;
    private $phone;
    private $id;
    //private $db;
    
    
    // public function __construct(
    //     //$db,
    //     string $firstname, 
    //     string $surname,
    //     string $phone
    // ) {
    //     //$this->db = $db;
    //     $this->firstname = $firstname;
    //     $this->surname = $surname;
    //     $this->phone = $phone;
    // }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getSurname(): string {
        return $this->surname;
    }
    
    public function getPhone(): string {
        return $this->phone;
    }
    
    public function getId(): string {
        return $this->id;
    }
    
    public function setFirstname(string $firstname) {
        $this->firstname = $firstname;
    }

    public function setSurname(string $surname) {
        $this->surname = $surname;
    }    
    
    public function setPhone(string $phone) {
        $this->phone = $phone;
    }    

}