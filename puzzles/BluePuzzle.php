<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class BluePuzzle
 * The blue puzzle at work
 *
 * @package DLX\Puzzles
 */
class BluePuzzle extends Polyominoes3D
{

	/**
	 * This uses the ABCDEF naming scheme
	 *
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'A' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 1)), array(array(0, 0, 0, 0), array(0, 0, 0, 1)))),
		'B' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0)), array(array(1, 0, 0, 1), array(0, 0, 0, 1)))),
		'C' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0), array(1, 1, 0, 0), array(1, 0, 0, 0)), array(array(0, 0, 0, 0), array(1, 1, 0, 0), array(0, 0, 0, 0), array(1, 0, 0, 0)))),
		'D' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 1), array(1, 0, 0, 0), array(1, 1, 1, 0)), array(array(0, 0, 0, 1), array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)))),
		'E' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(0, 1, 1, 0), array(0, 1, 0, 0), array(1, 1, 1, 0)), array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 1, 1, 0)))),
		'F' => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 1), array(1, 0, 0, 0), array(1, 1, 1, 0)), array(array(1, 0, 0, 0), array(0, 0, 0, 1), array(0, 0, 0, 0), array(1, 1, 0, 0)))),
	);

	/**
	 * @var int
	 */
	protected $size = 64;


	/**
	 * A custom layout can be passed as a 3D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param int $layers optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Pentominoes3D
	 */
	public function __construct($cols = 4, $rows = 4, $layers = 4, $symmetry = false) {
		try {
			parent::__construct($cols, $rows, $layers, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
