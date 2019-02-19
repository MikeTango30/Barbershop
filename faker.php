<?php
    // require the Faker autoloader
    require_once 'vendor/autoload.php';

$config = new Config();

$dbConfig = $config->get('db');

$db = new PDO(
            'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['name'].'',
            $dbConfig['user'],
            $dbConfig['password']
        );
    
// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

//generate fake data
for ($i = 0; $i < 100; $i++) 
    {
        $q = "INSERT INTO clients (name, lastname, email, phone, address, city, country) VALUES(
        '".$faker->name."',
        '".$faker->lastname."',
        '".$faker->email."',
        '".$faker->e164PhoneNumber."',
        '".$faker->streetAddress."',
        '".$faker->city."',
        '".$faker->country."'
        )";
        // insert into db
        $sth = $this->db->prepare($q);
        $sth->execute();
    }    
?>