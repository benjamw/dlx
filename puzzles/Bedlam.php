<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Bedlam
 * A 3D polycube puzzle
 *
 * @see https://en.wikipedia.org/wiki/Bedlam_cube
 * @package DLX\Puzzles
 */
class Bedlam extends Polyominoes3D
{

	/**
	 * [count, mirror, symmetry, points array]
	 *     points are a 3D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		// PHP gets confused about arrays with digit indexes when they're strings
		// so make the piece names more than just the digits to prevent errors
		' 0 ' => [1, false, 4, [[[1, 1, 0], [0, 1, 1], [0, 1, 0]]] ], // green F
		' 1 ' => [1,  true, 1, [[[0, 1, 0], [1, 1, 1], [0, 1, 0]]] ], // purple X
		' 2 ' => [1,  true, 4, [[[1, 1, 0], [0, 1, 1], [0, 0, 1]]] ], // black W
		' 3 ' => [1, false, 4, [[[1, 0], [1, 1], [0, 1]], [[1, 0], [0, 0], [0, 0]]] ], // black
		' 4 ' => [1,  true, 4, [[[1, 1, 1], [0, 1, 0]], [[0, 1, 0], [0, 0, 0]]] ], // purple
		' 5 ' => [1, false, 4, [[[1, 1, 0], [0, 1, 0]], [[0, 1, 1], [0, 0, 0]]] ], // purple
		' 6 ' => [1, false, 4, [[[1, 1, 1], [0, 1, 0]], [[0, 0, 0], [0, 1, 0]]] ], // gray
		' 7 ' => [1, false, 4, [[[1, 1, 1], [1, 0, 0]], [[1, 0, 0], [0, 0, 0]]] ], // black
		' 8 ' => [1,  true, 4, [[[1, 1, 1], [1, 0, 0]], [[0, 0, 1], [0, 0, 0]]] ], // orange
		' 9 ' => [1, false, 4, [[[1, 1, 1], [0, 0, 1]], [[0, 0, 0], [0, 0, 1]]] ], // green
		' A ' => [1,  true, 4, [[[1, 1, 0], [0, 0, 0]], [[0, 1, 0], [0, 1, 1]]] ], // orange
		' B ' => [1, false, 4, [[[1, 1, 1], [1, 0, 0]], [[0, 1, 0], [0, 0, 0]]] ], // gray
		' C ' => [1,  true, 4, [[[1, 1], [0, 1]], [[1, 0], [0, 0]]] ], // gray
	];

	/**
	 * @var int
	 */
	protected int $size = 64;


	/**
	 * @param bool $symmetry
	 *
	 * @throws Exception
	 */
	public function __construct(bool $symmetry = true) {
		// this puzzle is always a 4x4x4 cube
		parent::__construct(4, 4, 4, $symmetry);
	}

}
