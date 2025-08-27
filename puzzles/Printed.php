<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Printed
 * A small 3D polyomino puzzle
 *
 * @note the gray printed puzzle at work
 * @package DLX\Puzzles
 */
class Printed extends Polyominoes3D
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'Y' => [4, false, 4, [[[1, 1], [1, 0]], [[0, 1], [1, 0]]]],
		'Z' => [2, false, 4, [[[1, 1], [1, 0]], [[0, 1], [0, 0]]]],
		'O' => [1, false, 1, [[[1, 1], [1, 1]]]],
	];

	/**
	 * @var int
	 */
	protected int $size = 32;


	/**
	 * @param void
	 *
	 * @throws Exception
	 */
	public function __construct( ) {
		// this puzzle is always a 4x4x2 cube
		try {
			parent::__construct(4, 4, 2, true);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
