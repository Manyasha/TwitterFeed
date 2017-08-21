<?php

class db
{
  public $dbh;
  public $error;
  public $error_msg;    

  function __construct() {

    require_once('config.php');
    
    if($this->dbh = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)) {
            
      // Set every possible option to utf-8
      mysqli_query($this->dbh, 'SET NAMES "utf8"');
      mysqli_query($this->dbh, 'SET CHARACTER SET "utf8"');
      mysqli_query($this->dbh, 'SET character_set_results = "utf8",' .
        'character_set_client = "utf8", character_set_connection = "utf8",' .
        'character_set_database = "utf8", character_set_server = "utf8"');
    } else {
      $this->error = true;
      $this->error_msg = 'Unable to connect to DB';
      $this->log_error('__construct','attempted connection to ' . $db_name);
    }
        
    date_default_timezone_set(TIME_ZONE);
  }

  private function error_test($function,$query) {
    if ($this->error_msg = mysqli_error($this->dbh)) {
        $this->log_error($function,$query);
        $this->error = true;
    } else {
        $this->error = false;
    }
    return $this->error;
  }

  private function log_error($function,$query) {
    $fp = fopen('error_log.txt','a');
    fwrite($fp, date(DATE_RFC822) . ' | ' . 
      $_SERVER["SCRIPT_NAME"] . ' -> ' . $function . 
      ' | ' . $this->error_msg . ' | ' . $query . "\n");
    fclose($fp); 
  }

  public function date($php_date) {
    return date('Y-m-d H:i:s', strtotime($php_date));	
  }

  public function escape($str) {
    return mysqli_real_escape_string($this->dbh,$str);
  }

  public function in_table($table,$where) {
    $query = 'SELECT * FROM ' . $table . 
      ' WHERE ' . $where;
    $result = mysqli_query($this->dbh,$query);
    $this->error_test('in_table',$query); 
    return mysqli_num_rows($result) > 0;
  }

  public function select($query) {
    $result = mysqli_query( $this->dbh, $query );
    $this->error_test("select",$query);
    return $result;
  }

  public function insert($table,$field_values) {
    $query = 'INSERT INTO ' . $table . ' SET ' . $field_values;
    mysqli_query($this->dbh,$query);
    $this->error_test('insert',$query);
  }
}  
