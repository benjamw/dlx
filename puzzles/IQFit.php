<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class IQFit
 *
 *
 * @see http://mypuzzlecollection.blogspot.com/2014/09/iq-fit.html
 * @package DLX\Puzzles
 */
class IQFit extends Polyominoes
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'A (lt. green)' => array(1, false, 4, array(array(array(1, 1, 1), array(1, 0, 0)), array(array(1, 0, 1), array(0, 0, 0)))), // light olive green
		'B (dk. blue)'  => array(1, false, 4, array(array(array(1, 1, 1), array(0, 1, 0)), array(array(1, 0, 1), array(0, 0, 0)))), // dark blue
		'C (dk. green)' => array(1, false, 4, array(array(array(1, 1, 1), array(0, 1, 0)), array(array(0, 1, 1), array(0, 0, 0)))), // dark green
		'D (purple)'    => array(1, false, 4, array(array(array(1, 1, 1), array(0, 0, 1)), array(array(1, 1, 0), array(0, 0, 0)))), // purple
		'E (blue)'      => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0)), array(array(1, 0, 1, 0), array(0, 0, 0, 0)))), // blue (med.)
		'F (red)'       => array(1, false, 4, array(array(array(1, 1, 1, 1), array(1, 0, 0, 0)), array(array(1, 0, 0, 1), array(0, 0, 0, 0)))), // red
		'G (lt. blue)'  => array(1, false, 4, array(array(array(1, 1, 1, 1), array(0, 1, 0, 0)), array(array(0, 1, 1, 0), array(0, 0, 0, 0)))), // light blue
		'H (orange)'    => array(1, false, 4, array(array(array(1, 1, 1, 1), array(0, 1, 0, 0)), array(array(0, 1, 0, 1), array(0, 0, 0, 0)))), // orange
		'I (pink)'      => array(1, false, 4, array(array(array(1, 1, 1, 1), array(0, 0, 1, 0)), array(array(0, 0, 1, 1), array(0, 0, 0, 0)))), // pink
		'J (yellow)'    => array(1, false, 4, array(array(array(1, 1, 1, 1), array(0, 0, 0, 1)), array(array(1, 1, 0, 0), array(0, 0, 0, 0)))), // yellow
	);

	/**
	 * @var int
	 */
	protected $size = 50;


	/**
	 * The puzzle is a 10 x 5 rectangle
	 *
	 * @param int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return IQFit
	 */
	public function __construct($cols = 10, $rows = 5, $symmetry = false) {
		if (is_bool($cols)) {
			$symmetry = $cols;
			$cols = 10;
		}

		try {
			parent::__construct($cols, $rows, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Rotate the piece and create the node rows
	 * If $fixed is true, this will only create 2 node rows for the piece
	 * thereby eliminating rotation and reflection symmetry in the solution set
	 *
	 * @param string $pieceName
	 * @param array $piece
	 * @param array $nodes reference
	 * @param bool $fixed fix this piece in only 2 orientations
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
		$points = $piece[self::PIECE_POINTS][0]; // use only the first layer
		$done = $rotated = false;

		// any of the pieces can be used to fix the solutions

		while ( ! $done) {
			switch ($piece[self::PIECE_SYMMETRY]) {
				case 4 : // 90 degree symmetry
					// rotate the piece 180 degrees and place it
					$points = self::rotatePiece(180, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and once more, 90 this time
					$points = self::rotatePiece(90, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and rotate it another 90 to get back to start for the fall through
					$points = self::rotatePiece(90, $points);

					if ($fixed) {
						break;
					}
					// no break

				case 2 : // 180 degree symmetry
					// rotate the piece 90 degrees and place it
					$points = self::rotatePiece(90, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and rotate it back to start for the fall through
					$points = self::rotatePiece(-90, $points);
					// no break

				case 1 : // rotationally symmetric
					// no break
				default :
					// no rotation, just put the piece on the board
					$this->placePiece($pieceName, $points, $nodes);
					break;
			}

			$done = true;

			// switch to the second orientation and do it again
			if ( ! $rotated) {
				$points = self::rotateX($piece[self::PIECE_POINTS]);
				$points = $points[0];
				$rotated = true;
				$done = false;
			}
		}
	}

	/**
	 * Rotate the given points 90 degrees about the X axis
	 *
	 * @param array $points
	 *
	 * @return array
	 */
	public static function rotateX($points) {
		$pointsX = array( );
		$cntZ = count($points);
		$cntY = count($points[0]);

		// this method is about 25% faster than the following:
		// $pointsX = call_user_func_array('array_map', array(-1 => null) + array_reverse($points));

		for ($y = 0; $y < $cntY; ++$y) {
			for ($z = $cntZ - 1; $z >= 0; --$z) {
				$pointsX[$y][$cntZ - $z - 1] = $points[$z][$y];
			}
		}

		return $pointsX;
	}

}
