<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class LonposPyramid
 * Lonpos puzzles are a distinct set of polyomino puzzles that come in several form factors
 *
 * This class solves the 5x5 3D pyramid puzzle on the back of most containers with the orthogonally linked smooth balls
 * This is NOT the pyramid created with the linked cubes, or the isometrically linked dodecahedrons
 *
 * @see http://en.wikipedia.org/wiki/Lonpos
 * @package DLX\Puzzles
 */
class LonposPyramid extends Polyominoes3D
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		// L is first because it's the hardest to place
		'L' => array(1, false, 1, array(array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0)))), // gray X
		'A' => array(1,  true, 4, array(array(array(1, 1, 1), array(1, 0, 0)))), // orange L
		'B' => array(1,  true, 4, array(array(array(1, 1, 1), array(1, 1, 0)))), // red P
		'C' => array(1,  true, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0)))), // dk. blue L
		'D' => array(1,  true, 4, array(array(array(1, 1, 1, 1), array(0, 1, 0, 0)))), // pink Y
		'E' => array(1,  true, 4, array(array(array(1, 1, 1, 0), array(0, 0, 1, 1)))), // green N
		'F' => array(1, false, 4, array(array(array(1, 0), array(1, 1)))), // white V
		'G' => array(1, false, 4, array(array(array(1, 0, 0), array(1, 0, 0), array(1, 1, 1)))), // lt. blue V
		'H' => array(1, false, 4, array(array(array(1, 0, 0), array(1, 1, 0), array(0, 1, 1)))), // magenta W
		'I' => array(1, false, 4, array(array(array(1, 1, 1), array(1, 0, 1)))), // yellow U
		'J' => array(1, false, 2, array(array(array(1, 1, 1, 1)))), // purple I
		'K' => array(1, false, 1, array(array(array(1, 1), array(1, 1)))), // lime O
	);

	/**
	 * These are hand-rotated vertical orientations for each of the pieces
	 *
	 * @var array
	 */
	public static $ROTATIONS = array(
		'A' => array(
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 0, 0)),
				array(array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(1, 0)),
				array(array(0, 0), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 1), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 0, 0), array(1, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0)),
				array(array(0, 0, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 1), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 1), array(0, 0, 0)),
				array(array(0, 1, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(1, 0)),
				array(array(0, 1), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
		),
		'B' => array(
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 0), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 1), array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 1, 0), array(1, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0)),
				array(array(0, 1, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 1), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 1, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 1), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
		),
		'C' => array(
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 1), array(0, 0, 0, 0)),
				array(array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 0), array(1, 0)),
				array(array(0, 0), array(0, 0), array(1, 0), array(0, 0)),
				array(array(0, 0), array(1, 0), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 0), array(0, 1), array(0, 0), array(0, 0)),
				array(array(0, 1), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 1), array(0, 0, 1, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0)),
				array(array(0, 0, 0, 0), array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 1, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0)),
				array(array(0, 0, 0, 0), array(1, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 0), array(0, 1), array(0, 0), array(0, 0)),
				array(array(0, 1), array(0, 0), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 1), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 1, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(1, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(1, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
		),
		'D' => array(
			array(
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0, 1), array(0, 0, 1, 0)),
				array(array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 0), array(0, 0), array(1, 0), array(0, 0)),
				array(array(0, 0), array(1, 0), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 0), array(0, 1), array(1, 0), array(0, 0)),
				array(array(0, 1), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 1, 0)),
				array(array(0, 0, 1, 0), array(0, 1, 0, 0)),
				array(array(0, 0, 0, 0), array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 1, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0)),
				array(array(0, 1, 0, 0), array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 0), array(0, 1), array(0, 0), array(0, 0)),
				array(array(0, 1), array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 1), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 1, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(1, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(1, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
		),
		'E' => array(
			array(
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0, 1), array(0, 0, 0, 0)),
				array(array(0, 0, 1, 0), array(0, 1, 0, 0)),
				array(array(0, 1, 0, 0), array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(1, 0)),
				array(array(0, 0), array(0, 1), array(1, 0), array(0, 0)),
				array(array(0, 1), array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 1, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0)),
				array(array(0, 1, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 0), array(0, 1), array(0, 0), array(0, 0)),
				array(array(0, 0), array(1, 0), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
			),
			array(
				array(array(0, 0, 0, 1), array(0, 0, 1, 0)),
				array(array(0, 0, 1, 0), array(0, 1, 0, 0)),
				array(array(0, 0, 0, 0), array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 0), array(0, 1), array(1, 0), array(0, 0)),
				array(array(0, 1), array(0, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 1, 0)),
				array(array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 1, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(0, 1), array(0, 0)),
				array(array(0, 0), array(0, 0), array(1, 0), array(0, 0)),
				array(array(0, 0), array(1, 0), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0), array(0, 0)),
			),
		),
		'F' => array(
			array(
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 1), array(1, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 1), array(1, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(1, 0)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(0, 0), array(1, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 1), array(0, 0)),
				array(array(1, 0), array(0, 0)),
			),
		),
		'G' => array(
			array(
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 1), array(0, 0, 0), array(1, 0, 0)),
				array(array(0, 1, 0), array(1, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 1)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 0, 1), array(0, 0, 0), array(1, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 1, 0)),
				array(array(0, 0, 0), array(0, 0, 0), array(1, 0, 0)),
				array(array(0, 0, 0), array(1, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 1, 0)),
				array(array(0, 0, 0), array(0, 0, 0), array(1, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 1), array(0, 0, 0)),
				array(array(0, 0, 1), array(0, 0, 0), array(0, 0, 0)),
				array(array(0, 1, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
		),
		'H' => array(
			array(
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 1)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 0, 1), array(0, 1, 0), array(1, 0, 0)),
			),
			array(
				array(array(0, 0, 1), array(0, 1, 0), array(1, 0, 0)),
				array(array(0, 1, 0), array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 1), array(0, 0, 0)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(0, 1, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 1, 0)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(0, 0, 0), array(1, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
		),
		'I' => array(
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 0, 0)),
				array(array(0, 1, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 0), array(1, 0)),
				array(array(0, 1), array(1, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 0, 0), array(1, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 0, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0), array(0, 0), array(0, 1)),
				array(array(0, 0), array(0, 1), array(1, 0)),
				array(array(0, 1), array(0, 0), array(0, 0)),
				array(array(1, 0), array(0, 0), array(0, 0)),
			),
		),
		'J' => array(
			array(
				array(array(1)),
				array(array(1)),
				array(array(1)),
				array(array(1)),
			),
			array(
				array(array(0, 0, 0, 1)),
				array(array(0, 0, 1, 0)),
				array(array(0, 1, 0, 0)),
				array(array(1, 0, 0, 0)),
			),
			array(
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 1)),
				array(array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 0)),
				array(array(0, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
				array(array(1, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0), array(0, 0, 0, 0)),
			),
			array(
				array(array(0), array(0), array(0), array(1)),
				array(array(0), array(0), array(1), array(0)),
				array(array(0), array(1), array(0), array(0)),
				array(array(1), array(0), array(0), array(0)),
			),
		),
		'K' => array(
			array(
				array(array(0, 0), array(0, 1)),
				array(array(1, 0), array(0, 1)),
				array(array(1, 0), array(0, 0))
			),
			array(
				array(array(0, 0), array(0, 1)),
				array(array(0, 1), array(1, 0)),
				array(array(1, 0), array(0, 0))
			),
		),
		'L' => array(
			array(
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 1)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 0)),
			),
			array(
				array(array(0, 0, 0), array(0, 0, 1), array(0, 1, 0)),
				array(array(0, 0, 0), array(0, 1, 0), array(0, 0, 0)),
				array(array(0, 1, 0), array(1, 0, 0), array(0, 0, 0)),
			),
		),
	);

	/**
	 * @var int
	 */
	protected $size = 55;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param void $cols
	 * @param void $rows
	 * @param void $layers
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return LonposPyramid
	 */
	public function __construct($cols = null, $rows = null, $layers = null, $symmetry = false) {
		if (is_bool($cols)) {
			$symmetry = $cols;
		}

		// because there can only be one pyramid layout, it's hard-coded here
		// (and it appears upside down so the z index goes "up")
		$cols = array(
			array(
				array(1, 1, 1, 1, 1),
				array(1, 1, 1, 1, 1),
				array(1, 1, 1, 1, 1),
				array(1, 1, 1, 1, 1),
				array(1, 1, 1, 1, 1),
			),
			array(
				array(1, 1, 1, 1, 0),
				array(1, 1, 1, 1, 0),
				array(1, 1, 1, 1, 0),
				array(1, 1, 1, 1, 0),
				array(0, 0, 0, 0, 0),
			),
			array(
				array(1, 1, 1, 0, 0),
				array(1, 1, 1, 0, 0),
				array(1, 1, 1, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
			),
			array(
				array(1, 1, 0, 0, 0),
				array(1, 1, 0, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
			),
			array(
				array(1, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
				array(0, 0, 0, 0, 0),
			),
		);

		try {
			parent::__construct($cols, 0, 0, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Pull all the hard-coded rotations and place each piece using those
	 *
	 * @param string $pieceName
	 * @param array $piece
	 * @param array $nodes reference
	 * @param bool $fixed fix this piece in only 2 orientations
	 *
	 * @throws Exception
	 * @return void
	 */
	public function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
		// placePiece will ignore any duplicates that may be generated here
		$this->usedNodes = array( );

		$points = $piece[self::PIECE_POINTS];

		// the flat rotations
		for ($i = 0; $i < 4; ++$i) {
			$points = self::rotateZ($points);
			$this->placePiece($pieceName, $points, $nodes);
		}

		// the 3D rotations/orientations (hand-coded)
		for ($i = 0, $len = count(self::$ROTATIONS[$pieceName]); $i < $len; ++$i) {
			$points = self::$ROTATIONS[$pieceName][$i];
			$this->placePiece($pieceName, $points, $nodes);
		}
	}

}
