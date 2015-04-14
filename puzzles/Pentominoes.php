<?php

namespace DLX\Puzzles;

use \Exception;

class Pentominoes extends Polyominoes
{

	/**
	 * This uses the FILNPTUVWXYZ naming scheme
	 *
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'X' => array(1, false, 1, array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0))),
		'F' => array(1,  true, 4, array(array(1, 0, 0), array(1, 1, 1), array(0, 1, 0))),
		'L' => array(1,  true, 4, array(array(1, 1, 1, 1), array(1, 0, 0, 0))),
		'N' => array(1,  true, 4, array(array(1, 1, 1, 0), array(0, 0, 1, 1))),
		'P' => array(1,  true, 4, array(array(1, 1, 1), array(1, 1, 0))),
		'Y' => array(1,  true, 4, array(array(1, 1, 1, 1), array(0, 1, 0, 0))),
		'Z' => array(1,  true, 2, array(array(1, 1, 0), array(0, 1, 0), array(0, 1, 1))),
		'T' => array(1, false, 4, array(array(1, 1, 1), array(0, 1, 0), array(0, 1, 0))),
		'U' => array(1, false, 4, array(array(1, 1, 1), array(1, 0, 1))),
		'V' => array(1, false, 4, array(array(1, 0, 0), array(1, 0, 0), array(1, 1, 1))),
		'W' => array(1, false, 4, array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1))),
		'I' => array(1, false, 2, array(array(1, 1, 1, 1, 1))),
	);

	/**
	 * @var int
	 */
	protected $size = 60;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Pentominoes
	 */
	public function __construct($cols = 10, $rows = 6, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;

			if (6 !== $rows) {
				$cols = $rows;
				$rows = $symmetry;
			}
			else {
				$cols = 10;
			}

			$symmetry = $temp;
		}
		elseif (is_bool($rows)) {
			// $cols was a full layout
			$symmetry = $rows;
			$rows = 6;
		}

		try {
			parent::__construct($cols, $rows, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
