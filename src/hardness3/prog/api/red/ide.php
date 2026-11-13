<?php
namespace hardness;

$ide_base_dir = 'ide/api/';
$ide_file_path = str_replace('api/red/ide/', '', $_REQUEST['r']);
$file_path_to_require = $ide_base_dir . $ide_file_path . '.php';

if (strpos($file_path_to_require, '..') !== false) {
    return;
};

require_once($file_path_to_require);