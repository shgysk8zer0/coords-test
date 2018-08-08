<?php
namespace Config;
spl_autoload_register('spl_autoload');
const BASE = __DIR__ . DIRECTORY_SEPARATOR;
const CLASSES_DIR = BASE . 'classes';
const CREDS = [
	'user'     => 'coords',
	'password' => 'myPassword',
];

set_include_path(CLASSES_DIR . PATH_SEPARATOR . get_include_path());

require_once BASE . 'functions.php';
