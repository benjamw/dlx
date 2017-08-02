<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Trominoes
 * Trominoes are a form of polyominoes puzzles with 3 squares
 *
 * This class solves the flat 2D trominoes puzzles
 *
 * @see http://en.wikipedia.org/wiki/Tromino
 * @package DLX\Puzzles
 */
class Trominoes extends Polyominoes
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'L' => array(2, false, 4, array(array(1, 0), array(1, 1))),
		'I' => array(2, false, 2, array(array(1, 1, 1))),
	);

	/**
	 * @var int
	 */
	protected $size = 12;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 *
	 * @throws Exception
	 * @return Trominoes
	 */
	public function __construct($cols = 4, $rows = 3) {
		try {
			parent::__construct($cols, $rows);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
