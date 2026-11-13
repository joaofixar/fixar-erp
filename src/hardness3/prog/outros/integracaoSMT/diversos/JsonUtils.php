<?php
namespace hardness;

class JsonUtils {
  public function array2Json($array) {
    return json_encode($array);
  }

  public function json2Array($json) {
    return json_decode($json);
  }
}