<?php
namespace Search;
require_once 'config.php';

use \shgysk8zer0\Core\{Headers, PDO};
use \shgysk8zer0\Core_API\Abstracts\{HTTPStatusCodes};
use const \Config\{CREDS};
use function \Functions\{search_coords};
use \Coords;
use \DateTime;

$headers = new Headers();

if ($headers->accept !== 'application/json') {
	http_response_code(HTTPStatusCodes::NOT_ACCEPTABLE);
} elseif (array_key_exists('search', $_GET) and is_array($_GET['search'])) {
	$location = $_GET['search'];
	$radius = floatval($location['radius']);
	$coords = new Coords($location['longitude'], $location['latitude']);
	$pdo = new PDO(CREDS);
	$headers->content_type = 'application/json';

	$results = search_coords($coords, $radius, $pdo);
	echo json_encode($results);
} else {
	http_response_code(HTTPStatusCodes::BAD_REQUEST);
}
