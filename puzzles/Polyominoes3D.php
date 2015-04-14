<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

abstract class Polyominoes3D extends Polyominoes
{

	/**
	 * $PIECES array indexes
	 */
	const PIECE_COUNT = 0;
	const PIECE_REFLECT = 1;
	const PIECE_HORIZ_SYMMETRY = 2;
	const PIECE_VERT_SYMMETRY = 3;
	const PIECE_POINTS = 4;

	/**
	 * To be filled by child classes...
	 *
	 * array(count, mirror, horiz. symmetry, vert. symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array( );


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param int $layers optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Polyominoes3D
	 */
	public function __construct($cols = 0, $rows = 0, $layers = 0, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;
			$cols = $rows;
			$rows = $layers;
			$layers = $symmetry;
			$symmetry = $temp;
		}
		elseif (is_bool($rows)) {
			// $cols was a full layout
			$symmetry = $rows;
			$rows = 0;
			$layers = 0;
		}

		$this->symmetry = $symmetry;

		try {
			$this->createLayout($cols, $rows, $layers);
			$this->createGrid( );
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * The layout can be passed as a 3D array into $cols
	 * or as row and column and layer dimensions
	 *
	 * If a layout is passed as an array, the indexes for the array
	 * should be valid x,y,z coordinates for the board. '0' is allowed to
	 * block out certain positions without having to break the array.
	 *
	 *     $cols[$z][$y][$x] => (x, y, z)
	 *
	 * Because creating 3D strings would be... strange, strings
	 * are not allowed as input, yet.
	 *
	 * @param array|int $cols
	 * @param int $rows
	 * @param int $layers
	 *
	 * @throws Exception
	 * @return void
	 */
	public function createLayout($cols, $rows, $layers) {
		if (is_array($cols)) {
			$count = 0;

			// this is a simple dimensions test
			// this does not take into account the checkerboard validity of the layout
			foreach ($cols as $layer) {
				foreach ($layer as $col) {
					foreach ($col as $node) {
						if (1 === $node) {
							++$count;
						}
					}
				}
			}

			if ($this->size !== $count) {
				throw new Exception('Invalid layout size');
			}

			$this->layout = $cols;
		}
		else {
			$this->layout = array( );

			if ($this->size !== ($cols * $rows * $layers)) {
				throw new Exception('Invalid layout size');
			}

			for ($j = 0; $j < $layers; ++$j) {
				$this->layout[$j] = array( );

				for ($i = 0; $i < $rows; ++$i) {
					$this->layout[$j][] = array_fill(0, $cols, 1);
				}
			}
		}

		// because the layout may have holes in it, or other odd shapes,
		// the grid indexes may not line up with the actual layout indexes
		// so a translation array is needed
		$this->translate = self::createTranslationArray($this->layout);
	}

	/**
	 * Create the col names used to translate the solutions
	 *
	 * @param void
	 *
	 * @return void
	 */
	public function createColNames( ) {
		$colNames = array(''); // cols are 1-index

		foreach (static::$PIECES as $pieceName => $pieceData) {
			if (1 === $pieceData[self::PIECE_COUNT]) {
				$colNames[] = $pieceName;
			}
			else {
				for ($i = 1; $i <= $pieceData[self::PIECE_COUNT]; ++$i) {
					$colNames[] = $pieceName.'('.$i.')';
				}
			}
		}

		foreach ($this->layout as $layer => $layers) {
			foreach ($layers as $row => $cols) {
				foreach ($cols as $col => $value) {
					if ( ! $value) {
						continue;
					}

					$colNames[] = "[" . ($col + 1) . "," . ($row + 1) . "," . ($layer + 1) ."]";
				}
			}
		}

		$this->colNames = $colNames;
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	public function createNodes( ) {
		$nodes = array( );

		$beenFixed = ! $this->symmetry;

		foreach (static::$PIECES as $pieceName => $piece) {
			if (1 === $piece[self::PIECE_COUNT]) {
				$fixed = ( ! $beenFixed && (4 === $piece[self::PIECE_SYMMETRY]) && (false === $piece[self::PIECE_REFLECT]));
				$beenFixed = $beenFixed || $fixed;
				$this->createPieceNodes($pieceName, $piece, $nodes, $fixed);
			}
			else {
				for ($i = 1; $i <= $piece[self::PIECE_COUNT]; ++$i) {
					$fixed = ( ! $beenFixed && (4 === $piece[self::PIECE_SYMMETRY]) && (false === $piece[self::PIECE_REFLECT]));
					$beenFixed = $beenFixed || $fixed;
					$this->createPieceNodes($pieceName.'('.$i.')', $piece, $nodes, $fixed);
				}
			}
		}

		return $nodes;
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
	public function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
		$points = $piece[self::PIECE_POINTS];
		$done = $reflected = false;

		// only pieces with no rotational or reflectional symmetry should be fixed
		if ($fixed && (4 !== $piece[self::PIECE_SYMMETRY]) && (false === $piece[self::PIECE_REFLECT])) {
			$fixed = false;
		}

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

			// if the piece does not have reflection symmetry, reflect the piece and do it all again
			if ( ! $fixed && ! $reflected && $piece[self::PIECE_REFLECT]) {
				$points = self::reflectPiece($piece[self::PIECE_POINTS]);
				$reflected = true;
				$done = false;
			}
		}
	}

	/**
	 * @param $layout
	 *
	 * @return array
	 */
	public static function createTranslationArray($layout) {
		$n = 0;
		$translate = array( );
		foreach ($layout as $z => $layer) {
			$width = count($layer);

			foreach ($layer as $y => $row) {
				$len = count($row);

				foreach ($row as $x => $space) {
					if (1 === $space) {
						$translate[$x + ($y * $len) + ($z * ($len * $width))] = $n;
						++$n;
					}
				}
			}
		}

		return $translate;
	}

	/**
	 * Rotate the given piece points about the origin
	 * the given number of degrees (90 = CW)
	 *
	 * @param int $degrees
	 * @param array $points
	 *
	 * @throws Exception
	 * @return array points
	 */
	public static function rotatePiece($degrees, $points) {
		$points = array_values($points); // keys need to be clean
		$points = array_map('array_values', $points);

		switch ((int) $degrees) {
			case -90 :
				// watch out for magic...
				$points = call_user_func_array('array_map', array(-1 => null) + array_map('array_reverse', $points));
				break;

			case 0 :
				// do nothing
				break;

			case 90 :
				// watch out for magic...
				$points = call_user_func_array('array_map', array(-1 => null) + array_reverse($points));
				break;

			case 180 :
				$points = array_map('array_reverse', $points);
				$points = array_reverse($points);
				break;

			default :
				throw new Exception('Rotation value ('.$degrees.') not supported.');
		}

		// make sure it's a 2D array
		// this really only applies to the horizontal I piece after rotation
		if ( ! is_array($points[0])) {
			// this wants so bad to be magical, but a loop will have to do...
			foreach ($points as & $point) { // mind the reference
				$point = (array) $point;
			}
			unset($point); // kill the reference
		}

		return $points;
	}

	/**
	 * Reflect the given piece points
	 *
	 * @param array $points
	 *
	 * @return array points
	 */
	public static function reflectPiece($points) {
		return array_reverse($points);
	}

	/**
	 * Places the given piece into the nodes
	 * trying every possible position within the layout
	 *
	 * @param string $pieceName
	 * @param array $points
	 * @param array $nodes reference
	 *
	 * @return void
	 */
	public function placePiece($pieceName, $points, & $nodes) {
		$boardWidth = count($this->layout[0][0]);
		$boardHeight = count($this->layout[0]);
		$boardDepth = count($this->layout);

		$pieceWidth = count($points[0][0]);
		$pieceHeight = count($points[0]);
		$pieceDepth = count($points);

		// do some quick fit validity tests
		if (
			($boardWidth < $pieceWidth) ||
			($boardHeight < $pieceHeight) ||
			($boardDepth < $pieceDepth)
		) {
			// the piece is too big to fit
			// don't fail, just don't place this piece
			return;
		}

		$width = ($boardWidth - $pieceWidth) + 1; // +1 for fence posts
		$height = ($boardHeight - $pieceHeight) + 1; // +1 for fence posts
		$depth = ($boardDepth - $pieceDepth) + 1; // +1 for fence posts

		// this is like O(log(n)^inf*2)... sorry about that :(
		// but check out those indents...
		// it's turtles all the way down
		for ($z = 0; $z < $depth; ++$z) {
			for ($y = 0; $y < $height; ++$y) {
				for ($x = 0; $x < $width; ++$x) {
					$boardNodes = array();

					for ($pz = 0; $pz < $pieceDepth; ++$pz) {
						for ($py = 0; $py < $pieceHeight; ++$py) {
							for ($px = 0; $px < $pieceWidth; ++$px) {
								if (1 === $points[$pz][$py][$px]) {
									if (0 === $this->layout[$z + $pz][$y + $py][$x + $px]) {
										// the piece doesn't fit here, move along...
										continue 4;
									}

									$index = ($x + $px) + (($y + $py) * $boardWidth) + (($z + $pz) * ($boardWidth * $boardHeight));
									$boardNodes[] = $this->translate[$index];
								}
							}
						}
					}

					$nodes[] = $this->createNodeRow($pieceName, $boardNodes);
				}
			}
		}
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
		foreach (static::$PIECES as $pieceName => $piece) {
			echo '<div style="clear:both;">'. $pieceName .':</div>';

			$points = $piece[self::PIECE_POINTS];
			$done = $reflected = false;

			while ( ! $done) {
				switch ($piece[self::PIECE_SYMMETRY]) {
					case 4 : // 90 degree symmetry
						// rotate the piece 180 degrees and place it
						$points = self::rotatePiece(180, $points);
						self::printPiece($points);

						// and once more, 90 this time
						$points = self::rotatePiece(90, $points);
						self::printPiece($points);

						// and rotate it another 90 to get back to start for the fall through
						$points = self::rotatePiece(90, $points);
					// no break

					case 2 : // 180 degree symmetry
						// rotate the piece 90 degrees and place it
						$points = self::rotatePiece(90, $points);
						self::printPiece($points);

						// and rotate it back to start for the fall through
						$points = self::rotatePiece(-90, $points);
					// no break

					case 1 : // rotationally symmetric
						// no break
					default :
						// no rotation, just put the piece on the board
						self::printPiece($points);
				}

				$done = true;

				// if the piece does not has reflection symmetry, do it all again
				if ( ! $reflected && $piece[self::PIECE_REFLECT]) {
					$points = self::reflectPiece($piece[self::PIECE_POINTS]);
					$reflected = true;
					$done = false;
				}
			}
		}

		echo '<hr style="clear:both;">';
	}

	/**
	 * Debugging function to print a given piece
	 *
	 * @param $piece
	 *
	 * @return void
	 */
	public static function printPiece($piece) {
		echo '<table style="float:left;width:auto;height:auto;margin:10px;border-spacing:0;border-collapse:collapse;"><tbody>';

		foreach ($piece as $row) {
			echo '<tr>';

			if (is_array($row)) {
				foreach ($row as $value) {
					echo '<td style="padding:0;width:30px;height:30px;background:' . ($value ? (2 == $value ? 'red' : 'black') : 'white') . ';border:1px solid lightgray;">&nbsp;</td>';
				}
			}

			echo '</tr>';
		}

		echo '</tbody></table>';
	}

}
