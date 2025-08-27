<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Seven
 * A small 3D polyomino puzzle
 *
 * @see https://www.youtube.com/watch?v=wOk7dTcJEx8
 * @package DLX\Puzzles
 */
class Seven extends Polyominoes3D
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 3D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'I' => [1, false, 2, [[[1, 1, 1]]]],
		'L' => [1,  true, 4, [[[1, 1, 1], [1, 0, 0]]]],
		'O' => [1, false, 1, [[[1, 1], [1, 1]]]],
		'S' => [1,  true, 4, [[[1, 1, 0], [0, 1, 1]]]],
		'T' => [1, false, 4, [[[1, 1, 1], [0, 1, 0]]]],
		'U' => [1, false, 4, [[[1, 1, 1], [1, 0, 1]]]],
		'V' => [1, false, 4, [[[1, 0], [1, 1]]]],
	];

	/**
	 * @var int
	 */
	protected int $size = 27;


	/**
	 * @param bool $symmetry exclude symmetrical solutions
	 *
	 * @throws Exception
	 */
	public function __construct(bool $symmetry = true) {
		// this puzzle is always a 3x3x3 cube
		try {
			parent::__construct(3, 3, 3, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
