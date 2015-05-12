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
	public static $PIECES = array(
		'Y' => array(4, false, 4, array(array(array(1, 1), array(1, 0)), array(array(0, 1), array(1, 0)))),
		'Z' => array(2, false, 4, array(array(array(1, 1), array(1, 0)), array(array(0, 1), array(0, 0)))),
		'O' => array(1, false, 1, array(array(array(1, 1), array(1, 1)))),
	);

	/**
	 * @var int
	 */
	protected $size = 32;


	/**
	 * @param void
	 *
	 * @throws Exception
	 * @return Printed
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
