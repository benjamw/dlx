<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Pentominoes
 * Pentominoes are a form of polyominoes puzzles with 5 squares
 *
 * This class solves the flat 2D pentominoes puzzles
 *
 * @see http://en.wikipedia.org/wiki/Pentomino
 * @package DLX\Puzzles
 */
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
	public static array $PIECES = [
		// X is first because it's the hardest to place
		'X' => [1, false, 1, [[0, 1, 0], [1, 1, 1], [0, 1, 0]]],
		'F' => [1,  true, 4, [[1, 0, 0], [1, 1, 1], [0, 1, 0]]],
		'L' => [1,  true, 4, [[1, 1, 1, 1], [1, 0, 0, 0]]],
		'N' => [1,  true, 4, [[1, 1, 1, 0], [0, 0, 1, 1]]],
		'P' => [1,  true, 4, [[1, 1, 1], [1, 1, 0]]],
		'Y' => [1,  true, 4, [[1, 1, 1, 1], [0, 1, 0, 0]]],
		'Z' => [1,  true, 2, [[1, 1, 0], [0, 1, 0], [0, 1, 1]]],
		'T' => [1, false, 4, [[1, 1, 1], [0, 1, 0], [0, 1, 0]]],
		'U' => [1, false, 4, [[1, 1, 1], [1, 0, 1]]],
		'V' => [1, false, 4, [[1, 0, 0], [1, 0, 0], [1, 1, 1]]],
		'W' => [1, false, 4, [[1, 0, 0], [1, 1, 0], [0, 1, 1]]],
		'I' => [1, false, 2, [[1, 1, 1, 1, 1]]],
	];

	/**
	 * @var int
	 */
	protected int $size = 60;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
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
