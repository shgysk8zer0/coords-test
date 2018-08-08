<?php
namespace Functions;
use \Coords;
use \DateTime;
use \PDO;
use \StdClass;

function search_coords(
	Coords $center,
	Float $radius,
	PDO $pdo
): Array
{
	$stm = $pdo->prepare(
		'SELECT `longitude`, `latitude`, `dtime`
		FROM `coords`
		WHERE (`longitude` BETWEEN :x1 AND :x2)
		AND (`latitude` BETWEEN :y1 AND :y2)
		AND SQRT(
			POWER(`longitude` - :lon, 2) + POWER(`latitude` - :lat, 2)
		) <= :radius
		LIMIT 20;'
	);
	$stm->bindValue(':x1', $center->longitude - $radius);
	$stm->bindValue(':x2', $center->longitude + $radius);
	$stm->bindValue(':y1', $center->latitude - $radius);
	$stm->bindValue(':y2', $center->latitude + $radius);
	$stm->bindValue(':lon', $center->longitude);
	$stm->bindValue(':lat', $center->latitude);
	$stm->bindValue(':radius', $radius);
	$stm->execute();

	return array_map(function(StdClass $result): StdClass
	{
		$item = new StdClass();
		$dtime = new DateTime($result->dtime);
		$item->coords = new Coords($result->longitude, $result->latitude);
		$item->dtime = $dtime->format(DateTime::ATOM);
		return $item;
	}, $stm->fetchAll(PDO::FETCH_CLASS));
}
