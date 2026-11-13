<?php
namespace hardness;
log('-> ' . $_REQUEST['loading']);
$loading = isset($_REQUEST['loading']) ? ($_REQUEST['loading']+1)*50 : 0;
$g['contador'] = 0 + $loading;
function gContador($array){
	global $g;
	$g['contador']++;
	return $g['contador'];
}