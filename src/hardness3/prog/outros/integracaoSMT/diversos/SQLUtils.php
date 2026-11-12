<?php
namespace hardness;

class SQLUtils {
  public function execute_sql($sql) {
    return mysql_query($sql);
  }

  public function fetch_row($query) {
    return mysql_fetch_assoc($query);
  }

  public function transaction_start() {
    $this->execute_sql("SET AUTOCOMMIT=0;");
    $this->execute_sql("START TRANSACTION;");

    return true;
  }

  public function transaction_close($commit = true) {
    if ($commit) {
      $this->execute_sql("COMMIT;");
    } else {
      $this->execute_sql("ROLLBACK;");
    }

    $this->execute_sql("SET AUTOCOMMIT=1;");

    return true;
  }

  public function sql2Json($sql) {
    return $this->array2Json($this->sql2Array($sql));
  }

  public function sql2Array($sql) {
    $sqlArray = array();

    if (!empty($sql)) {
      $query = $this->execute_sql($sql);
      
      while ($result = $this->fetch_row($query)) {
        array_push($sqlArray, $result);
      }
    }

    return $sqlArray;
  }

  public function array2Json($array) {
		require_once(__DIR__ . "/../diversos/JsonUtils.php");
		
		$JsonUtils = new JsonUtils();
    return $JsonUtils->array2Json($array);
  }
}