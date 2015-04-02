<?php

namespace DLX\Puzzles;

use \Exception;

class Lonpos extends Polyominoes
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		// L is first because it's the hardest to place
		'L' => array(1, false, 1, array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0))),
		'A' => array(1,  true, 4, array(array(1, 1, 1), array(1, 0, 0))),
		'B' => array(1,  true, 4, array(array(1, 1, 1), array(1, 1, 0))),
		'C' => array(1,  true, 4, array(array(1, 1, 1, 1), array(1, 0, 0, 0))),
		'D' => array(1,  true, 4, array(array(1, 1, 1, 1), array(0, 1, 0, 0))),
		'E' => array(1,  true, 4, array(array(1, 1, 1, 0), array(0, 0, 1, 1))),
		'F' => array(1, false, 4, array(array(1, 1), array(1, 0))),
		'G' => array(1, false, 4, array(array(1, 1, 1), array(1, 0, 0), array(1, 0, 0))),
		'H' => array(1, false, 4, array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1))),
		'I' => array(1, false, 4, array(array(1, 1, 1), array(1, 0, 1))),
		'J' => array(1, false, 2, array(array(1, 1, 1, 1))),
		'K' => array(1, false, 1, array(array(1, 1), array(1, 1))),
	);

	/**
	 * @var int
	 */
	protected $size = 55;

	/**
		The layout to pass in for the colorful cabin would be the following:

		$cols = array(
			array(1, 1, 1, 1, 1, 1, 0, 0, 0),
			array(1, 1, 1, 1, 1, 1, 1, 0, 0),
			array(1, 1, 1, 1, 1, 1, 1, 1, 0),
			array(1, 1, 0, 1, 1, 1, 1, 1, 1),
			array(1, 1, 1, 1, 1, 1, 1, 1, 1),
			array(1, 1, 1, 1, 1, 1, 1, 0, 0),
			array(0, 1, 1, 1, 1, 1, 0, 0, 0),
			array(0, 0, 1, 1, 1, 0, 0, 0, 0),
			array(0, 0, 0, 1, 1, 0, 0, 0, 0),
		);

		- OR -

		$cols = "
			........
			.******.
			.*******.
			.********.
			.**.******.
			.*********.
			.*******.
			..*****.
			...***.
			....**.
			.......
		";


		and for the crazy cone it would be the following:

		$cols = array(
			array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
			array(1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
			array(1, 1, 1, 1, 1, 1, 1, 1, 0, 0),
			array(1, 1, 1, 1, 1, 1, 1, 0, 0, 0),
			array(1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
			array(1, 1, 1, 1, 1, 0, 0, 0, 0, 0),
			array(1, 1, 1, 1, 0, 0, 0, 0, 0, 0),
			array(1, 1, 1, 0, 0, 0, 0, 0, 0, 0),
			array(1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
			array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0),
		);

		- OR -

		$cols = "
			............
			.**********.
			.*********.
			.********.
			.*******.
			.******.
			.*****.
			.****.
			.***.
			.**.
			.*.
			...
		";

	 **/


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 *
	 * @throws Exception
	 * @return Lonpos
	 */
	public function __construct($cols = 11, $rows = 5) {
		try {
			parent::__construct($cols, $rows);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
