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
	public static $PIECES = array(
		'L' => array(1,  true, 4, array(array(array(1, 1, 1), array(1, 0, 0)))), // blue
		'B' => array(1,  true, 4, array(array(array(1, 0), array(1, 1)), array(array(1, 0), array(0, 0)))), // white
		'P' => array(1, false, 4, array(array(array(1, 1), array(1, 0)), array(array(1, 0), array(0, 0)))), // orange
		'T' => array(1, false, 4, array(array(array(1, 1, 1), array(0, 1, 0)))), // yellow
		'Z' => array(1,  true, 2, array(array(array(1, 1, 0), array(0, 1, 1)))), // brown
		'V' => array(1, false, 4, array(array(array(1, 1), array(1, 0)))), // red
		'O' => array(1, false, 4, array(array(array(1, 1), array(1, 1)))), // yellow from abaroth
	);

	/**
	 * @var int
	 */
	protected $size = 27;


	/**
	 * @param bool $symmetry
	 *
	 * @throws Exception
	 * @return IQPuzzle
	 */
	public function __construct($symmetry = true) {
		// this puzzle is always a 3x3x3 cube
		try {
			parent::__construct(3, 3, 3, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
