<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Abaroth
 * A small 3D polyomino puzzle
 *
 * @see http://abarothsworld.com/Puzzles/Polycubes/Abaroths%20Cube.htm
 * @package DLX\Puzzles
 */
class Abaroth extends Polyominoes3D
{

	/**
	 * [count, mirror, symmetry, points array]
	 *     points are a 3D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'O' => [1, false, 1, [[[1, 1], [1, 1]]]], // yellow
		'Q' => [1,  true, 4, [[[1, 1], [1, 0]], [[0, 1], [0, 0]]]], // pink/purple -- black
		'Y' => [1, false, 4, [[[1, 1], [1, 0]], [[1, 0], [0, 0]]]], // blue
		'T' => [1, false, 4, [[[1, 1, 1], [0, 1, 0], [0, 1, 0]]]], // red
		'W' => [1, false, 4, [[[1, 0, 0], [1, 1, 0], [0, 1, 1]]]], // orange -- white
		'Z' => [1,  true, 4, [[[1, 1, 1], [0, 0, 1]], [[1, 0, 0], [0, 0, 0]]]], // green -- brown
	];

	/**
	 * @var int
	 */
	protected int $size = 27;


	/**
	 * @throws Exception
	 */
	public function __construct( ) {
		// this puzzle is always a 3x3x3 cube
		parent::__construct(3, 3, 3, true);
	}

}
