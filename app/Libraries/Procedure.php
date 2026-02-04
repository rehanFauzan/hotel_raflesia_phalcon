<?php

namespace App\Libraries;

use Phalcon\Db;
use Phalcon\Di;

class Procedure
{

	function executeProcedure($procedureName, $params = [])
	{

		$placeholder = array_reduce(
			array_keys($params),
			function ($acc, $item) {
				$acc[] = '@' . $item . ' := ?';
				return $acc;
			},
			[]
		);

		$sql = 'CALL ' . $procedureName . ' (' . join(', ', $placeholder) . ')';
		// $db = Di::getDefault()->get("db");
		$query = $db->query($sql, array_values($params));
		// $query->setFetchMode(
		// 	Db::FETCH_ASSOC
		// );

		return $query;
	}
}
