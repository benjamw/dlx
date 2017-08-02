<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class FullPolyominoes
 * This uses all polyominoes from 1 to 5 squares
 *
 * This class solves the flat 2D polyominoes puzzles
 *
 * @see http://en.wikipedia.org/wiki/Pentomino
 * @package DLX\Puzzles
 */
class FullPolyominoes extends Polyominoes
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
	// monomino
		'1' => array(1, false, 4, array(array(1))),

	// domino
		'2' => array(1, false, 2, array(array(1, 1))),

	// trominoes
		'<' => array(2, false, 4, array(array(1, 0), array(1, 1))),
		'|' => array(2, false, 2, array(array(1, 1, 1))),

	// tetrominoes
		'i' => array(1, false, 2, array(array(1, 1, 1, 1))),
		'o' => array(1, false, 1, array(array(1, 1), array(1, 1))),
		't' => array(1, false, 4, array(array(1, 1, 1), array(0, 1, 0))),
		'l' => array(1, false, 2, array(array(1, 1, 1), array(1, 0, 0))),
		'z' => array(1, false, 2, array(array(1, 1, 0), array(0, 1, 1))),

	// pentominoes (5)
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
	protected $size = 89;


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
	public function __construct($cols = 11, $rows = 8, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;

			if (8 !== $rows) {
				$cols = $rows;
				$rows = $symmetry;
			}
			else {
				$cols = 11;
			}

			$symmetry = $temp;
		}
		elseif (is_array($cols) || (is_string($cols) && ((string) (int) $cols !== $cols))) {
			// $cols was a full layout
			if (is_bool($rows)) {
				$symmetry = $rows;
			}
			else {
				$symmetry = false;
			}

			$rows = 8;
		}

		try {
			parent::__construct($cols, $rows, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
