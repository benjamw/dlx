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
		'L' => array(1, false, 1, array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0))), // gray X
		'A' => array(1,  true, 4, array(array(1, 1, 1), array(1, 0, 0))), // orange L
		'B' => array(1,  true, 4, array(array(1, 1, 1), array(1, 1, 0))), // red P
		'C' => array(1,  true, 4, array(array(1, 1, 1, 1), array(1, 0, 0, 0))), // dk. blue L
		'D' => array(1,  true, 4, array(array(1, 1, 1, 1), array(0, 1, 0, 0))), // pink Y
		'E' => array(1,  true, 4, array(array(1, 1, 1, 0), array(0, 0, 1, 1))), // green N
		'F' => array(1, false, 4, array(array(1, 0), array(1, 1))), // white V
		'G' => array(1, false, 4, array(array(1, 0, 0), array(1, 0, 0), array(1, 1, 1))), // lt. blue V
		'H' => array(1, false, 4, array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1))), // magenta W
		'I' => array(1, false, 4, array(array(1, 1, 1), array(1, 0, 1))), // yellow U
		'J' => array(1, false, 2, array(array(1, 1, 1, 1))), // purple I
		'K' => array(1, false, 1, array(array(1, 1), array(1, 1))), // lime O
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
			..
		";

	 **/


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Lonpos
	 */
	public function __construct($cols = 11, $rows = 5, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;

			if (5 !== $rows) {
				$cols = $rows;
				$rows = $symmetry;
			}
			else {
				$cols = 11;
			}

			$symmetry = $temp;
		}
		elseif (is_bool($rows)) {
			// $cols was a full layout
			$symmetry = $rows;
			$rows = 5;
		}

		try {
			parent::__construct($cols, $rows, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
