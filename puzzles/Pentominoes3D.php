<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Pentominoes3D
 * Pentominoes are a form of polyominoes puzzles with 5 squares
 *
 * This class solves the 3D pentominoes puzzles
 *
 * @see http://en.wikipedia.org/wiki/Pentomino
 * @package DLX\Puzzles
 */
class Pentominoes3D extends Polyominoes3D
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
		// X is first because it's the hardest to place
		'X' => array(1, false, 1, array(array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0)))),
		'F' => array(1,  true, 4, array(array(array(1, 0, 0), array(1, 1, 1), array(0, 1, 0)))),
		'L' => array(1,  true, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0)))),
		'N' => array(1,  true, 4, array(array(array(1, 1, 1, 0), array(0, 0, 1, 1)))),
		'P' => array(1,  true, 4, array(array(array(1, 1, 1), array(1, 1, 0)))),
		'Y' => array(1,  true, 4, array(array(array(1, 1, 1, 1), array(0, 1, 0, 0)))),
		'Z' => array(1,  true, 2, array(array(array(1, 1, 0), array(0, 1, 0), array(0, 1, 1)))),
		'T' => array(1, false, 4, array(array(array(1, 1, 1), array(0, 1, 0), array(0, 1, 0)))),
		'U' => array(1, false, 4, array(array(array(1, 1, 1), array(1, 0, 1)))),
		'V' => array(1, false, 4, array(array(array(1, 0, 0), array(1, 0, 0), array(1, 1, 1)))),
		'W' => array(1, false, 4, array(array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1)))),
		'I' => array(1, false, 2, array(array(array(1, 1, 1, 1, 1)))),
	);

	/**
	 * @var int
	 */
	protected $size = 60;


	/**
	 * A custom layout can be passed as a 3D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param int $layers optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Pentominoes3D
	 */
	public function __construct($cols = 5, $rows = 4, $layers = 3, $symmetry = false) {
		try {
			parent::__construct($cols, $rows, $layers, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
