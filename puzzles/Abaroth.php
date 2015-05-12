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
	 * array(count, symmetry, points array)
	 *     points are a 3D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the BNW corner (bottom north west)
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'O' => array(1, false, 1, array(array(array(1, 1), array(1, 1)))), // yellow
		'T' => array(1, false, 4, array(array(array(1, 1, 1), array(0, 1, 0), array(0, 1, 0)))), // red
		'W' => array(1, false, 4, array(array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1)))), // orange
		'Q' => array(1,  true, 4, array(array(array(1, 1), array(1, 0)), array(array(0, 1), array(0, 0)))), // // pink/purple
		'Y' => array(1, false, 4, array(array(array(1, 1), array(1, 0)), array(array(1, 0), array(0, 0)))), // blue
		'Z' => array(1,  true, 4, array(array(array(1, 1, 1), array(0, 0, 1)), array(array(1, 0, 0), array(0, 0, 0)))), // green
	);

	/**
	 * @var int
	 */
	protected $size = 27;


	/**
	 * @param void
	 *
	 * @throws Exception
	 * @return Abaroth
	 */
	public function __construct( ) {
		// this puzzle is always a 3x3x3 cube
		try {
			parent::__construct(3, 3, 3, true);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
