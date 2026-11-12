<?php
namespace hardness;

session_start(); 
session_destroy(); 
header("Location: index.php"); 