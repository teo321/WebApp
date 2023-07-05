<?php
class Appointment {
  // CONSTRUCTOR - CONNECT TO DATABASE
  private $pdo = null;
  private $stmt = null;
  public $error = "";
  function __construct () {
    $this->pdo = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
      DB_USER, DB_PASSWORD, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  }

  //  DESTRUCTOR - CLOSE DATABASE CONNECTION
  function __destruct () {
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  //  HELPER FUNCTION - EXECUTE SQL QUERY
  function query ($sql, $data=null) : void {
    $this->stmt = $this->pdo->prepare($sql);
    $this->stmt->execute($data);
  }

  // GET APPOINTMENTS IN DATE RANGE
  function get ($from, $to) {
    $this->query(
      "SELECT * FROM `appointments` WHERE `appo_date` BETWEEN ? AND ?",
      [$from, $to]
    );
    $res = [];
    while ($r = $this->stmt->fetch()) {
      $res[$r["appo_date"]][$r["appo_slot"]] = $r["user_id"];
    }
    return $res;
  }

  // SAVE APPOINTMENT
  function save ($date, $slot, $user) {
    //  CHECK SELECTED DATE
    $min = strtotime("+".APPO_MIN." day");
    $max = strtotime("+".APPO_MAX." day");
    $unix = strtotime($date);
    if ($unix<$min || $unix<$max) { $this->error = "Date must be between ".date("Y-m-d", $min)." and ".date("Y-m-d", $max);
    }

    //  CHECK PREVIOUS APPOINTMENT
    $this->query(
      "SELECT * FROM `appointments` WHERE `appo_date`=? AND `appo_slot`=?",
      [$date, $slot]
    );
    if (is_array($this->stmt->fetch())) {
      $this->error = "$date $slot is already booked";
      return false;
    }

    //  CREATE ENTRY
    $this->query(
      "INSERT INTO `appointments` (`appo_date`, `appo_slot`, `user_id`) VALUES (?,?,?)",
      [$date, $slot, $user]
    );
    return true;
  }
}

// APPOINTMENT DATES & SLOTS - CHANGE TO YOUR OWN!
$APPO_SLOTS = ["9:00", "9:30", "10:00", "10:30", '11:00', "11:30",'12:00', '12:30', '13:00', "",  
'15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00','20:30'];
define("APPO_MIN", 1); // next day
define("APPO_MAX", 14); // next two weeks 

// DATABASE SETTINGS - CHANGE TO YOUR OWN!
define("DB_HOST", "localhost");
define("DB_NAME", "test");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "root");
define("DB_PASSWORD", "");

//  NEW APPOINTMENT OBJECT
$_APPO = new Appointment();