<?php
    // require the Faker autoloader
    require_once 'vendor/autoload.php';
    
   // use \DateTime;
    
// class Config 
// {
//     private $data;
    
//     public function __construct() {
//         $json = file_get_contents(__DIR__ . '/app.json');
//         $this->data = json_decode($json, true);
//     }

//     public function get($key) {
//         if (!isset($this->data[$key])) {
//             throw new NotFoundException("Key $key not in config.");
//         }
//         return $this->data[$key];
//     }
// }

// $config = new Config();

// $dbConfig = $config->get('db');

// $db = new PDO(
//             'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['name'].'',
//             $dbConfig['user'],
//             $dbConfig['password']
//         );
    
// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

$startTime = new DateTime();
$endTime = new DateTime();

$startDate = new DateTime("10:00");
$endTime = new DateTime("20:00");
$interval = new DateInterval('PT15M');
$period = new DatePeriod($startTime, $interval, $endTime);

//generate fake data
for ($i = 0; $i < 5; $i++) 
    {
        // $q = "INSERT INTO customer (firstname, surname, phone) VALUES(
        //     '".$faker->name."',
        //     '".$faker->lastname."',
        //     '".$faker->e164PhoneNumber."'
        // )";
        // // insert into db
        // $sth = $db->prepare($q);
        // $sth->execute();
        
        $interval = $faker->dateTimeInInterval($startDate("Y-m-d"), $interval, $timezone = null);
        var_dump($interval);
        $data = $faker->dateTimeInInterval($startDate = ("-1 month"), $interval = '15 minutes', $timezone = null);
        //var_dump($data);
        
        
        
        // $q = "INSERT INTO reservation (customer_id, reservationDate, arrivalTime) VALUES(
        //     '".$faker->date($format = 'Y-m-d', $max = '2019-03-08')."',
        //     '".$faker->name."',
        
    }    