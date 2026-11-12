<?php
namespace hardness;

function stripTags($array, $param=false){
	$texto = $array['campoAtual'];
	$texto = str_replace("&NBSP;", "&nbsp;", $texto);
	$texto = str_replace(array("<div><br></div>", "<DIV><BR></DIV>"), " ", $texto);
	$texto = strip_tags($texto);
	return '<span style="white-space:normal;">'.$texto.'</span>';
}