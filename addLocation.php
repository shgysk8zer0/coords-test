<?php
namespace AddLocation;
require_once 'config.php';
use \shgysk8zer0\Core\{Headers, PDO};
use \shgysk8zer0\Core_API\Abstracts\{HTTPStatusCodes};
use const \Config\{CREDS};
use \Coords;

$headers = new Headers();

if ($headers->accept !== 'application/json') {
	http_response_code(HTTPStatusCodes::NOT_ACCEPTABLE);
} elseif (array_key_exists('addLocation', $_POST) and is_array($_POST['addLocation'])) {
	$location = $_POST['addLocation'];
	$coords = new Coords($location['longitude'], $location['latitude']);
	$pdo = new PDO(CREDS);
	$stm = $pdo->prepare('INSERT INTO `coords` (`longitude`, `latitude`) VALUES (:lon, :lat);');
	$stm->bindValue(':lon', $coords->longitude);
	$stm->bindValue(':lat', $coords->latitude);
	$stm->execute();
	$headers->content_type = 'application/json';
	echo json_encode($coords);
} else {
	http_response_code(HTTPStatusCodes::BAD_REQUEST);
}
