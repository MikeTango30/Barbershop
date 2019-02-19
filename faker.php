<?php
    // require the Faker autoloader
    require_once 'vendor/autoload.php';


class AvailableTimes
{
    
    public $startTime;
    public $endTime;
    public $interval;
    public $period;
   
 
    public function __construct() {
        $this->startTime = new DateTime("2019-01-01 10:00");
        $this->endTime = new DateTime("2019-03-08 20:00");
        $this->interval = new DateInterval('PT15M');
        $this->period = new DatePeriod($this->startTime, $this->interval, $this->endTime);
       
    }
    
    private function modifyPeriod() {
        return $this->period = new DatePeriod(
            $this->startTime->modify("+1 days"), 
            $this->interval, 
            $this->endTime->modify("+1 days")
            );
    }
    
    public function getDay($day) {
        $startTime = new DateTime($day);
        $endTime = new DateTime($day);
        
        $startTime->setTime(10, 0);
        $endTime->setTime(20, 0);
        $period = new DatePeriod($startTime, $this->interval, $endTime);
        
        foreach($period as $dayTime) {
            $timesStr [$dayTime->format('Y-m-d H:i:s')] = $dayTime->format('Y-m-d H:i:s');
        }    
        return $timesStr;
    }
}
    
class Config 
{
    private $data;
    
    public function __construct() {
        $json = file_get_contents(__DIR__ . '/app.json');
        $this->data = json_decode($json, true);
    }

    public function get($key) {
        if (!isset($this->data[$key])) {
            throw new NotFoundException("Key $key not in config.");
        }
        return $this->data[$key];
    }
}

$config = new Config();
$availableTimes = new AvailableTimes;

$dbConfig = $config->get('db');

$db = new PDO(
            'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['name'].'',
            $dbConfig['user'],
            $dbConfig['password']
        );
    

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();


        for ($i = 0; $i < 5; $i++) {
            
            foreach ($availableTimes->period as $day) {
                $time[]  = $day->format("Y-m-d H:i:s");
                }
                $customerId = 641;
                $oneDay = $time[random_int(1, 6376)];
                $dayTimes = $availableTimes->getDay($oneDay);
                $randomTime = array_rand($dayTimes, 1);
                $arrivalTime = date("H:i:s", strtotime($randomTime));
                $reservationDate = date("Y-m-d", strtotime($randomTime));
    

                $customerId += 10;

             $q = "INSERT INTO customer (customer_id, firstname, surname, phone) VALUES(
                '".$customerId."',
                 '".$faker->name."',
                 '".$faker->lastname."',
                 '".$faker->e164PhoneNumber."'
             )";
             // insert into db
             $sth = $db->prepare($q);
             $sth->execute();


            for ($i = 0; $i < 5; $i++) {
                foreach ($availableTimes->period as $day) {
                $time[]  = $day->format("Y-m-d H:i:s");
                }
                $customerId = 641;
                $oneDay = $time[random_int(1, 6376)];
                $dayTimes = $availableTimes->getDay($oneDay);
                $randomTime = array_rand($dayTimes, 1);
                $arrivalTime = date("H:i:s", strtotime($randomTime));
                $reservationDate = date("Y-m-d", strtotime($randomTime));
                
                print "insert time: ". $reservationDate ." - ".$arrivalTime." </br>";

                 $q = "INSERT INTO reservation (customer_id, reservationDate, arrivalTime) VALUES(
                    '".$customerId."',
                    :reservationDate,
                    :arrivalTime'
                     )";
                $sth = $db->prepare($q);
                $sth->bindParam("reservationDate", $reservationDate, PDO::PARAM_STR);
                $sth->bindParam("arrivalTime", $arrivalTime, PDO::PARAM_STR);
                $sth->execute();     
            }

        }   