<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class IQPuzzle
 * A small 3D polyomino puzzle I found at a store
 * that is similar to the Soma puzzle with one piece different
 *
 * @package DLX\Puzzles
 */
class IQPuzzle extends Polyominoes3D
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 3D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'L' => [1,  true, 4, [[[1, 1, 1], [1, 0, 0]]]], // blue
		'B' => [1,  true, 4, [[[1, 0], [1, 1]], [[1, 0], [0, 0]]]], // white
		'P' => [1, false, 4, [[[1, 1], [1, 0]], [[1, 0], [0, 0]]]], // orange
		'T' => [1, false, 4, [[[1, 1, 1], [0, 1, 0]]]], // yellow
		'Z' => [1,  true, 2, [[[1, 1, 0], [0, 1, 1]]]], // brown
		'V' => [1, false, 4, [[[1, 1], [1, 0]]]], // red
		'O' => [1, false, 4, [[[1, 1], [1, 1]]]], // yellow from abaroth
	];

	/**
	 * @var int
	 */
	protected int $size = 27;


	/**
	 * @param bool $symmetry
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
