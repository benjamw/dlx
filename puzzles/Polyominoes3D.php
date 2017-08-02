<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

/**
 * Class Polyominoes3D
 * An abstract class that can be extended to solve
 * 3D polyomino puzzles with various shaped pieces and boards
 *
 * @package DLX\Puzzles
 */
abstract class Polyominoes3D extends Polyominoes
{

	/**
	 * @var array
	 */
	protected $usedNodes;

	/**
	 * @var bool
	 */
	protected $isCube = false;


	/**
	 * A custom layout can be passed as a 3D array into the first argument
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

		if (is_bool($layers)) {
			$symmetry = $layers;
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
	 * @param int $layers [NOT optional]
	 *
	 * @throws Exception
	 * @return void
	 */
	public function createLayout($cols, $rows, $layers = null) {
		if (is_array($cols)) {
			$count = 0;
			$hasHoles = false;

			// this is a simple dimensions test
			// this does not take into account the checkerboard validity of the layout
			$one = count($cols);
			foreach ($cols as $layer) {
				$two = count($layer);

				foreach ($layer as $col) {
					$three = count($col);

					foreach ($col as $node) {
						if (1 === $node) {
							++$count;
						}
						else {
							$hasHoles = true;
						}
					}
				}
			}

			if ($this->size !== $count) {
				throw new Exception('Invalid layout size');
			}

			$this->layout = $cols;
			$this->isCube = ! $hasHoles && ($one === $two) && ($one === $three);
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

			$this->isCube = ($cols === $rows) && ($cols === $layers);
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

// TODO: find a way to abstract out the "fixed" method so it can be overridden by child classes
// and create that inside the foreach loop here
		foreach (static::$PIECES as $pieceName => $piece) {
			if (1 === $piece[self::PIECE_COUNT]) {
				$fixed = ( ! $beenFixed && (4 === $piece[self::PIECE_SYMMETRY]) && (true === $piece[self::PIECE_REFLECT]));
				$beenFixed = $beenFixed || $fixed;
				$this->createPieceNodes($pieceName, $piece, $nodes, $fixed);
			}
			else {
				for ($i = 1; $i <= $piece[self::PIECE_COUNT]; ++$i) {
					$fixed = ( ! $beenFixed && (4 === $piece[self::PIECE_SYMMETRY]) && (true === $piece[self::PIECE_REFLECT]));
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
	 * @param bool $fixed fix this piece in only 6 orientations
	 *
	 * @throws Exception
	 * @return void
	 */
	public function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
		$points = $piece[self::PIECE_POINTS];

		// only pieces with no rotational or reflectional symmetry should be fixed
		if ($fixed && (4 !== $piece[self::PIECE_SYMMETRY]) && (false === $piece[self::PIECE_REFLECT])) {
			$fixed = false;
		}

		$this->usedNodes = array( );

		// placePiece will ignore any duplicates that may be generated here
		$placed = false; // prevent duplicates via symmetry and rotation
		for ($i = 0; $i < 2; ++$i) { // it only needs to rotate about Z once, the rest are all duplicates
			for ($j = 0; $j < 4; ++$j) {
				for ($k = 0; $k < 4; ++$k) {
					if ($placed) {
						break;
					}

					$this->placePiece($pieceName, $points, $nodes);
					$points = self::rotateX($points);

					if ($fixed) {
					    if ($this->isCube) {
						    break 3; // stop everything, we only need to place this piece once
						}

						$placed = true;
						break; // stop this round to place this piece 3 times, once for each axis
					}
				}

				$points = self::rotateY($points);

				if ($fixed) {
				    break; // stop this round to place this piece 3 times, once for each axis
				}
			}

			$points = self::rotateZ($points);

			if ($fixed) {
			    break; // stop this round to place this piece 3 times, once for each axis
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

	/**
	 * Rotate the given points 90 degrees about the Y axis
	 *
	 * @param array $points
	 *
	 * @return array
	 */
	public static function rotateY($points) {
		$pointsY = array( );
		$cntZ = count($points);
		$cntY = count($points[0]);
		$cntX = count($points[0][0]);

		// because rotateX was significantly faster than the 'array_map' version
		// one was not even searched for for this method

		for ($x = 0; $x < $cntX; ++$x) {
			for ($y = 0; $y < $cntY; ++$y) {
				for ($z = $cntZ - 1; $z >= 0; --$z) {
					$pointsY[$x][$y][$cntZ - $z - 1] = $points[$z][$y][$x];
				}
			}
		}

		return $pointsY;
	}

	/**
	 * Rotate the given points 90 degrees about the Z axis
	 *
	 * @param array $points
	 *
	 * @return array
	 */
	public static function rotateZ($points) {
		$pointsZ = array( );
		$cntZ = count($points);
		$cntY = count($points[0]);
		$cntX = count($points[0][0]);

		// because rotateX was significantly faster than the 'array_map' version
		// one was not even searched for for this method

		for ($z = 0; $z < $cntZ; ++$z) {
			for ($x = 0; $x < $cntX; ++$x) {
				for ($y = $cntY - 1; $y >= 0; --$y) {
					$pointsZ[$z][$x][$cntY - $y - 1] = $points[$z][$y][$x];
				}
			}
		}

		return $pointsZ;
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
		// if a 2D piece was submitted, convert to flat 3D
		if ( ! is_array($points[0][0])) {
			$points = array($points);
		}

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

					// check for duplicates
					sort($boardNodes);
					if (in_array($boardNodes, $this->usedNodes)) {
						continue;
					}

					$this->usedNodes[] = $boardNodes;

					$nodes[] = $this->createNodeRow($pieceName, $boardNodes);
				}
			}
		}
	}

	/**
	 * Adjust a flat 3D piece for 2D processing
	 * NOTE: the piece must be flat and in the 0 index plane
	 * or this function will remove part of the piece
	 *
	 * @param array $piece
	 *
	 * @return array
	 */
	public function adjustPieceFor2D($piece) {
		$piece[self::PIECE_POINTS] = $piece[self::PIECE_POINTS][0];
		return $piece;
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
