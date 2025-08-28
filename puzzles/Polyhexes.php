<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

/**
 * Class Polyhexes
 * An abstract class that can be extended to solve
 * flat 2D isometric polyomino (polyhex) puzzles with various shaped pieces and boards
 *
 * @see https://en.wikipedia.org/wiki/Polyhex_(mathematics)
 * @see https://www.redblobgames.com/grids/hexagons
 * @package DLX\Puzzles
 */
abstract class Polyhexes extends Polyominoes
{

	/**
	 * To be filled by child classes...
	 *
	 * [count, mirror, symmetry, points array]
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points should be oriented to put a 1 value in the NW corner
	 *     (
	 *       count -> the count of pieces in this puzzle of this shape
	 *       mirror -> should a mirror reflection be performed, this is false if the piece is its own reflection
	 *       symmetry -> how many 60 degree rotations before coming back to self (6, 3, 2, or 1)
	 *       points -> points array
	 *     )
	 *
	 * The points array should follow the hexagonal grid coordinate system which is just
	 * a rectangular grid coordinate system but with the top being canted to the left
	 * also called an "axial" coordinate system with coords $array[$r][$q]
	 * -------------------
	 *  \  0,0  0,1  0,2  \
	 *    \  1,0  1,1  1,2  \
	 *      \  2,0  2,1  2,2  \
	 *        ------------------
	 *
	 * @var array
	 */
	public static array $PIECES = [];

	/**
	 * @return array
	 *
	 * @throws Exception
	 */
	protected function createNodes( ) {
		$nodes = [];

		$beenFixed = ! $this->symmetry;

		foreach (static::$PIECES as $pieceName => $piece) {
			if (1 === $piece[self::PIECE_COUNT]) {
				$fixed = ( ! $beenFixed && (6 === $piece[self::PIECE_SYMMETRY]) && (true === $piece[self::PIECE_REFLECT]));
				$beenFixed = $beenFixed || $fixed;
				$this->createPieceNodes($pieceName, $piece, $nodes, $fixed);
			}
			else {
				for ($i = 1; $i <= $piece[self::PIECE_COUNT]; ++$i) {
					$fixed = ( ! $beenFixed && (6 === $piece[self::PIECE_SYMMETRY]) && (true === $piece[self::PIECE_REFLECT]));
					$beenFixed = $beenFixed || $fixed;
					$this->createPieceNodes($pieceName.'('.$i.')', $piece, $nodes, $fixed);
				}
			}
		}

		return $nodes;
	}

	/**
	 * Rotate the piece and create the node rows
	 * If $fixed is true, this will only create 3 node rows for the piece
	 * thereby eliminating rotation and reflection symmetry in the solution set
	 *
	 * @param string $pieceName
	 * @param array $piece
	 * @param array $nodes reference
	 * @param bool $fixed fix this piece in only 3 orientations
	 *
	 * @throws Exception
	 */
	protected function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
		$points = $piece[self::PIECE_POINTS];
		$done = $reflected = false;

		// only pieces with no rotational or reflectional symmetry should be fixed
		if ($fixed && (6 !== $piece[self::PIECE_SYMMETRY]) && (false === $piece[self::PIECE_REFLECT])) {
			$fixed = false;
		}

		while ( ! $done) {
			switch ($piece[self::PIECE_SYMMETRY]) {
				case 6 : // no symmetry (360 degree symmetry)
					// rotate the piece 180 degrees and place it
					$points = self::rotatePiece(180, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// rotate the piece 60 degrees and place it
					$points = self::rotatePiece(60, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and again...
					$points = self::rotatePiece(60, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and once more to return it to start for the fall through
					$points = self::rotatePiece(60, $points);
					// no break

				case 3 : // 180 degree symmetry
					// rotate the piece 120 degrees and place it
					$points = self::rotatePiece(120, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and rotate it back to start for the fall through
					$points = self::rotatePiece(-120, $points);
					// no break

				case 2 : // 120 degree symmetry
					// rotate the piece 60 degrees and place it
					$points = self::rotatePiece(60, $points);
					$this->placePiece($pieceName, $points, $nodes);

					// and rotate it back to start for the fall through
					$points = self::rotatePiece(-60, $points);
					// no break

				case 1 : // 60 degree symmetry (rotationally symmetric)
					// no break

				default :
					// no rotation, just put the piece on the board
					$this->placePiece($pieceName, $points, $nodes);
					break;
			}

			$done = true;

			// if the piece does not have reflection symmetry, reflect the piece and do it all again
			if ( ! $reflected && ! $fixed && $piece[self::PIECE_REFLECT]) {
				$points = self::reflectPiece($piece[self::PIECE_POINTS]);
				$reflected = true;
				$done = false;
			}
		}
	}

	/**
	 * Rotate the given piece points about the origin
	 * the given number of degrees (60 = CW)
	 *
	 * @param int $degrees
	 * @param array $points
	 *
	 * @throws Exception
	 * @return array points
	 */
	public static function rotatePiece(int $degrees, array $points) {
		$points = array_values($points); // keys need to be clean
		$points = array_map('array_values', $points);

		switch ((int) $degrees) {
			case -180:
				$points = self::rotatePiece(180, $points);
				break;

			case -120:
				$points = self::rotatePiece(240, $points);
				break;

			case -60:
				$points = self::rotatePiece(300, $points);
				break;

			case 0:
				// do nothing
				break;

			case 60:
				$points = self::rotatePiece60($points);
				break;

			case 120:
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				break;

			case 180:
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				break;

			case 240:
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				break;

			case 300:
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				break;

			case 360:
				// 360 degrees = 6 * 60 degrees, so rotate 6 times
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				$points = self::rotatePiece60($points);
				break;

			default:
				throw new Exception('Rotation value ('.$degrees.') not supported.');
		}

		// make sure it's a 2D array
		// this really only applies to the horizontal I piece after rotation
		if ( ! is_array($points[0])) {
			foreach ($points as & $point) { // mind the reference
				$point = (array) $point;
			}
			unset($point); // kill the reference
		}

		return $points;
	}

	/**
	 * Rotate the given piece by 60 degrees clockwise
	 * This is the core rotation method for hexagonal grid pieces
	 * Uses cubic coordinates for proper hexagonal rotation
	 *
	 * @param array $points The piece points array to rotate
	 *
	 * @return array points The rotated piece points array
	 */
	protected static function rotatePiece60(array $points) {
		$cubicPoints = self::arrayToCubicCoordinates($points);
		
		// for 60° CW rotation: q' = -r, r' = -s, s' = -q
		$rotatedCubicPoints = [];
		foreach ($cubicPoints as $point) {
			$q = $point[0];
			$r = $point[1];
			$s = $point[2];
			
			$rotatedCubicPoints[] = [-$r, -$s, -$q];
		}
		
		$rotatedArray = self::cubicCoordinatesToArray($rotatedCubicPoints);
		
		return self::trimArray($rotatedArray);
	}

	/**
	 * Convert 2D array indexes to cubic coordinates for hexagonal grid operations
	 * 
	 * This function converts from offset coordinates (y, x) to cube coordinates (q, r, s)
	 * where q + r + s = 0. This is useful for geometric transformations like rotation.
	 * 
	 * @param array $points 2D array where 1 indicates a filled position, 0 indicates empty
	 * 
	 * @return array Array of cubic coordinates [q, r, s] for each filled position
	 */
	public static function arrayToCubicCoordinates(array $points): array {
		$cubicPoints = [];
		
		// get cubic coordinates for all the filled positions in the points array
		$rows = count($points);
		for ($y = 0; $y < $rows; $y++) {
			$cols = count($points[$y]);
			for ($x = 0; $x < $cols; $x++) {
				if ($points[$y][$x] === 1) {
					$q = $x;
					$r = $y;
					$s = -($q + $r);
					
					$cubicPoints[] = [$q, $r, $s];
				}
			}
		}
		
		return $cubicPoints;
	}

	/**
	 * Convert cubic coordinates to 2D array indexes
	 * 
	 * This function converts from cubic coordinates (q, r, s) to a 2D array
	 * representation where 1 indicates a filled position and 0 indicates empty.
	 * 
	 * @param array $cubicPoints Array of cubic coordinates [q, r, s]
	 * @return array 2D array where 1 indicates filled positions, 0 indicates empty
	 */
	public static function cubicCoordinatesToArray(array $cubicPoints): array {
		if (empty($cubicPoints)) {
			return [];
		}
		
		// find the bounds of the array
		$minQ = PHP_INT_MAX;
		$maxQ = -PHP_INT_MAX;
		$minR = PHP_INT_MAX;
		$maxR = -PHP_INT_MAX;
		
		foreach ($cubicPoints as $pos) {
			$q = $pos[0];
			$r = $pos[1];
			
			$minQ = min($minQ, $q);
			$maxQ = max($maxQ, $q);
			$minR = min($minR, $r);
			$maxR = max($maxR, $r);
		}
		
		// calculate dimensions
		$rows = $maxR - $minR + 1;
		$cols = $maxQ - $minQ + 1;
		
		// initialize the 2D array with zeros
		$array = [];
		for ($y = 0; $y < $rows; $y++) {
			$array[$y] = array_fill(0, $cols, 0);
		}
		
		// fill in the positions where there are pieces
		foreach ($cubicPoints as $pos) {
			$q = $pos[0];
			$r = $pos[1];
			
			// convert to array indices
			$x = $q - $minQ;
			$y = $r - $minR;
			
			// set the position to 1
			$array[$y][$x] = 1;
		}
		
		return $array;
	}

	/**
	 * Trim empty rows and columns from the array
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	protected static function trimArray(array $array) {
		// find the bounds of the actual piece
		$minRow = PHP_INT_MAX;
		$maxRow = -PHP_INT_MAX;
		$minCol = PHP_INT_MAX;
		$maxCol = -PHP_INT_MAX;
		
		foreach ($array as $r => $row) {
			foreach ($row as $c => $val) {
				if ($val == 1) {
					$minRow = min($minRow, $r);
					$maxRow = max($maxRow, $r);
					$minCol = min($minCol, $c);
					$maxCol = max($maxCol, $c);
				}
			}
		}
		
		// extract the trimmed piece
		$trimmed = [];
		for ($r = $minRow; $r <= $maxRow; $r++) {
			$row = [];
			for ($c = $minCol; $c <= $maxCol; $c++) {
				$row[] = $array[$r][$c];
			}
			$trimmed[] = $row;
		}
		
		return $trimmed;
	}

	/**
	 * Reflect the given piece points about the q-axis
	 *
	 * This method converts the piece to cubic coordinates, reflects it about the q-axis,
	 * then converts back to axial coordinates and trims the result.
	 *
	 * @param array $points The piece points array to reflect
	 *
	 * @return array The reflected piece points array
	 */
	public static function reflectPiece(array $points) {
		$cubicPoints = self::arrayToCubicCoordinates($points);
		
		$reflectedCubicPoints = [];
		foreach ($cubicPoints as $point) {
			$q = $point[0];
			$r = $point[1];
			$s = $point[2];
			
			// reflect about the q-axis: q' = -q, r' = -s, s' = -r
			$reflectedCubicPoints[] = [-$q, -$s, -$r];
		}
		
		$reflectedArray = self::cubicCoordinatesToArray($reflectedCubicPoints);
		
		return self::trimArray($reflectedArray);
	}

	/**
	 * Debugging function to print out all pieces
	 * with all rotations and reflections
	 *
	 * @param void
	 *
	 * @throws Exception
	 * @return void
	 */
	public static function printPieces( ) {
		self::printCSS();

		foreach (static::$PIECES as $pieceName => $piece) {
			echo '<div class="piece-wrapper">';
			echo '<div class="name">'. $pieceName .':</div>';

			$points = $piece[self::PIECE_POINTS];
			$done = $reflected = false;

			while ( ! $done) {
				switch ($piece[self::PIECE_SYMMETRY]) {
					case 6 : // no symmetry (360 degree symmetry)
						for ($i = 0; $i < 6; ++$i) {
							self::printPiece($points);
							$points = self::rotatePiece(60, $points);
						}
						break;

					case 3 : // 120 degree symmetry
						for ($i = 0; $i < 3; ++$i) {
							self::printPiece($points);
							$points = self::rotatePiece(60, $points);
						}
						break;

					case 2 : // 180 degree symmetry
						for ($i = 0; $i < 2; ++$i) {
							self::printPiece($points);
							$points = self::rotatePiece(60, $points);
						}
						break;

					case 1 : // 60 degree symmetry (rotationally symmetric)
						// no break
					default :
						// no rotation, just put the piece on the board
						self::printPiece($points);
						break;
				}

				$done = true;

				// if the piece does not have reflection symmetry, do it all again
				if ( ! $reflected && $piece[self::PIECE_REFLECT]) {
					$points = self::reflectPiece($piece[self::PIECE_POINTS]);
					$reflected = true;
					$done = false;
					echo '<hr style="clear:both;">';
				}
			}

			echo '</div>'; // piece-wrapper
		}

		echo '<hr style="clear:both;">';
	}

	/**
	 * Debugging function to print a given piece
	 *
	 * @param array $piece
	 *
	 * @return void
	 */
	public static function printPiece($piece) {
		self::printCSS();

		echo '<div class="piece">';

		foreach ($piece as $col => $row) {
			echo '<div class="row">';

			for ($i = 0; $i < $col; ++$i) {
				echo '<div class="indent">&nbsp;</div>';
			}

			foreach ($row as $value) {
				echo '<div class="hex ' . ($value ? (2 == $value ? 'red' : 'black') : '') . '">&nbsp;</div>';
			}

			echo '</div>';
		}

		echo '</div>';
	}

	public static function printCSS( ) {
		static $printed = false;
		if ($printed) {
			return;
		}

		echo <<< EOCSS
		<style>
			.piece-wrapper {
				clear: both;
				margin-bottom: 20px;
				border-bottom: 1px solid darkgray;
			}
			.name {
				font-weight: bold;
				font-size: 1.5em;
				margin-bottom: 10px;
			}
			.piece {
				float: left;
				padding: 10px;
				border: 1px solid lightgoldenrodyellow;
			}
			.piece .row {
				height: auto;
				width: auto;
			}
			.indent {
				display: inline-block;
				width: 15px;
				height: 30px;
			}
			.hex {
				display: inline-block;
				width: 30px;
				height: 30px;
				border-radius: 50%;
				background: white;
				border: 1px solid lightgray;
			}
			.hex.black {
				background: black;
			}
			.hex.red {
				background: red;
			}
		</style>
		EOCSS;

		$printed = true;
	}

}
