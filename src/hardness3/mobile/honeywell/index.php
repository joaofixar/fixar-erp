<?php
namespace hardness;
$script = '';
global $g;
include('conf.php');

$usarLoginColetor = $g['C031']['usarLoginColetor'];

if($usarLoginColetor == 'S'){
	header('Location: ./validaSessao.php');
} else {
	header('Location: ./menu.php');
}