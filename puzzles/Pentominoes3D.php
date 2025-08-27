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
	public static array $PIECES = [
		// X is first because it's the hardest to place
		'X' => [1, false, 1, [[[0, 1, 0], [1, 1, 1], [0, 1, 0]]]],
		'F' => [1,  true, 4, [[[1, 0, 0], [1, 1, 1], [0, 1, 0]]]],
		'L' => [1,  true, 4, [[[1, 1, 1, 1], [1, 0, 0, 0]]]],
		'N' => [1,  true, 4, [[[1, 1, 1, 0], [0, 0, 1, 1]]]],
		'P' => [1,  true, 4, [[[1, 1, 1], [1, 1, 0]]]],
		'Y' => [1,  true, 4, [[[1, 1, 1, 1], [0, 1, 0, 0]]]],
		'Z' => [1,  true, 2, [[[1, 1, 0], [0, 1, 0], [0, 1, 1]]]],
		'T' => [1, false, 4, [[[1, 1, 1], [0, 1, 0], [0, 1, 0]]]],
		'U' => [1, false, 4, [[[1, 1, 1], [1, 0, 1]]]],
		'V' => [1, false, 4, [[[1, 0, 0], [1, 0, 0], [1, 1, 1]]]],
		'W' => [1, false, 4, [[[1, 0, 0], [1, 1, 0], [0, 1, 1]]]],
		'I' => [1, false, 2, [[[1, 1, 1, 1, 1]]]],
	];

	/**
	 * @var int
	 */
	protected int $size = 60;


	/**
	 * A custom layout can be passed as a 3D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param int $layers optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
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
