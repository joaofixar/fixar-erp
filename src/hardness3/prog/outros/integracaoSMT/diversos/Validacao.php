<?php
namespace hardness;

class Validacao {
  public function validarAcesso() {
    return $_GET['u'] == "63";
  }
}