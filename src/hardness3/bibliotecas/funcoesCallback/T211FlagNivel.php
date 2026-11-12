<?php
namespace hardness;

function T211FlagNivel($array) {
	switch ($array['campoAtual']) {
		case 1:
			return '<span style="color: red; font-weight: bold;">FATAL</span>';
		break;
		case 2:
			return '<span style="color: gray; font-weight: bold;">WARNING</span>';
		break;
		case 3:
			return '<span style="color: green;">DEBUG</span>';
		break;
	}
}