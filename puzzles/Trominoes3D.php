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
	public static $PIECES = array(
		'L' => array(2, false, 4, array(array(array(1, 0), array(1, 1)))),
		'I' => array(2, false, 2, array(array(array(1, 1, 1)))),
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
	 * @return Trominoes3D
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
