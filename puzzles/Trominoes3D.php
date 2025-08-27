<?php

namespace DLX\Puzzles;

use \Exception;

class Trominoes3D extends Polyominoes3D
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'L' => [2, false, 4, [[[1, 0], [1, 1]]]],
		'I' => [2, false, 2, [[[1, 1, 1]]]],
	];

	/**
	 * @var int
	 */
	protected int $size = 12;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param int $layers optional
	 *
	 * @throws Exception
	 */
	public function __construct($cols = 3, $rows = 2, $layers = 2) {
		try {
			parent::__construct($cols, $rows, $layers);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
