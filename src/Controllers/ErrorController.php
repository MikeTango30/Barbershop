<?php

namespace Barbershop\Controllers;

class ErrorController extends AbstractController 
{
    public function notFound(): string {
        $properties = ['errorMessage' => 'Page not found!'];
        return $this->render('error.twig', $properties);
    }
    
    public function requiredField(): string {
        $properties = ['errorMessage' => "Please fill this form!"];
        return $this->render('error.twig', $properties);
    }
}
