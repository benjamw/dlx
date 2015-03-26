<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

class Pentominoes
{

	/**
	 * $PIECES array indexes
	 */
	const PIECE_REFLECTION = 0;
	const PIECE_SYMMETRY = 1;
	const PIECE_POINTS = 2;

	/**
	 * This uses the FILNPTUVWXYZ naming scheme
	 *
	 * array(mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'F' => array( true, 4, array(array(1, 0, 0), array(1, 1, 1), array(0, 1, 0))),
		'I' => array(false, 2, array(array(1, 1, 1, 1, 1))),
		'L' => array( true, 4, array(array(1, 1, 1, 1), array(1, 0, 0, 0))),
		'N' => array( true, 4, array(array(1, 1, 1, 0), array(0, 0, 1, 1))),
		'P' => array( true, 4, array(array(1, 1, 1), array(1, 1, 0))),
		'T' => array(false, 4, array(array(1, 1, 1), array(0, 1, 0), array(0, 1, 0))),
		'U' => array(false, 4, array(array(1, 1, 1), array(1, 0, 1))),
		'V' => array(false, 4, array(array(1, 1, 1), array(1, 0, 0), array(1, 0, 0))),
		'W' => array(false, 4, array(array(1, 1, 0), array(0, 1, 1), array(0, 0, 1))),
		'X' => array(false, 1, array(array(0, 1, 0), array(1, 1, 1), array(0, 1, 0))),
		'Y' => array( true, 4, array(array(1, 1, 1, 1), array(0, 1, 0, 0))),
		'Z' => array( true, 2, array(array(1, 1, 0), array(0, 1, 0), array(0, 1, 1))),
	);

	/**
	 * @var array
	 */
	public $layout;

	/**
	 * @var \DLX\Grid
	 */
	public $grid;

	/**
	 * @var array
	 */
	public $colNames;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 *
	 * @throws Exception
	 * @return Pentominoes
	 */
	public function __construct($cols = 10, $rows = 6) {
		try {
			$this->createLayout($cols, $rows);
			$this->createGrid( );
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * The layout can be passed as a string or a 2D array into $cols
	 * or as row and column dimensions
	 *
	 * If a layout is passed as an array, the indexes for the array
	 * should be valid x,y coordinates for the board. '0' is allowed to
	 * block out certain positions without having to break the array.
	 *
	 *     $cols[$y][$x] => (x, y)
	 *
	 * @param string|array|int $cols
	 * @param int $rows
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function createLayout($cols, $rows) {
		if (is_string($cols)) {
			$this->layout = self::layoutToArray($cols);
		}
		elseif (is_array($cols)) {
			$count = 0;

			// this is a simple dimensions test
			// this does not take into account the checkerboard validity of the layout
			foreach ($cols as $col) {
				foreach ($col as $node) {
					if (1 === $node) {
						++$count;
					}
				}
			}

			if (60 !== $count) {
				throw new Exception('Invalid layout size');
			}

			$this->layout = $cols;
		}
		else {
			$this->layout = array( );

			if (60 !== ($cols * $rows)) {
				throw new Exception('Invalid layout size');
			}

			for ($i = 0; $i < $rows; ++$i) {
				$this->layout[] = array_fill(0, $cols, 1);
			}
		}
	}

	/**
	 * @param void
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function createGrid( ) {
		$this->createColNames( );
		$nodes = $this->createNodes( );

		try {
			$this->grid = new Grid($nodes, count($this->colNames) - 1);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Create the col names used to translate the solutions
	 *
	 * @param void
	 *
	 * @return void
	 */
	protected function createColNames( ) {
		$colNames = array(''); // cols are 1-index

		foreach (self::$PIECES as $pieceName => $pieceData) {
			$colNames[] = $pieceName;
		}

		foreach ($this->layout as $row => $cols) {
			foreach ($cols as $col => $value) {
				if ( ! $value) {
					continue;
				}

				$colNames[] = "[". ($row + 1) .",". ($col + 1) ."]";
			}
		}

		$this->colNames = $colNames;
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	protected function createNodes( ) {
		$nodes = array( );

		foreach (self::$PIECES as $pieceName => $piece) {
			$points = $piece[self::PIECE_POINTS];
			$done = $reflected = false;

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
				}

				$done = true;

				// if the piece does not have reflection symmetry, do it all again
				if ( ! $reflected && $piece[self::PIECE_REFLECTION]) {
					$points = self::reflectPiece($piece[self::PIECE_POINTS]);
					$reflected = true;
					$done = false;
				}
			}
		}

		return $nodes;
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	public function solve( ) {
		$this->grid->search( );
		return $this->getSolutions( );
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	public function getSolutions( ) {
		$solutions = $this->grid->getSolutions( );

		foreach ($solutions as & $solution) {
			sort($solution);

			foreach ($solution as & $path) {
				foreach ($path as & $col) {
					$col = $this->colNames[$col];
				}
			}
		}

		return $solutions;
	}

	/**
	 * Convert a layout given in string format
	 * to an array that the class can use
	 *
	 * @param $string
	 *
	 * @throws Exception
	 * @return array
	 */
	public static function layoutToArray($string) {
		$layout = array( );

		$string = preg_replace('%\r\n?%', "\n", $string);
		$lines = explode("\n", $string);

		// make sure every line begins and ends with a dot ( . )
		// and find the longest line
		$len = 0;
		$dots = true;
		foreach ($lines as $line) {
			if ('.' !== $line{0}) {
				$dots = false;
				break;
			}

			if ('.' !== $line{strlen($line) - 1}) {
				$dots = false;
				break;
			}

			if (strlen($line) > $len) {
				$len = strlen($line);
			}
		}

		// make sure the top and bottom lines are all dots
		if (false !== strpos($lines[0], '*')) {
			$dots = false;
		}

		if (false !== strpos($lines[count($lines) - 1], '*')) {
			$dots = false;
		}

		if ( ! $dots) {
			throw new Exception('Layout must be surrounded by dots');
		}

		foreach ($lines as $idx => $line) {
			if ((0 === $idx) || ($idx === (count($lines) - 1))) {
				continue;
			}

			$row = array_fill(0, $len, 0);

			$chars = explode('', trim($line));
			foreach ($chars as $jdx => $char) {
				if ((0 === $jdx) || ($jdx === (count($lines) - 1))) {
					continue;
				}

				if ('*' === $char) {
					$row[$jdx] = 1;
				}
			}

			$layout[] = $row;
		}

		return $layout;
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
		$points = array_values($points);
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
		// do some quick validity tests
		if (count($this->layout) < count($points)) {
			// don't fail, just don't place this piece
			return;
		}

		if (count($this->layout[0]) < count($points[0])) {
			// don't fail just don't place this piece
			return;
		}

		$boardWidth = count($this->layout[0]);
		$boardHeight = count($this->layout);

		$pieceWidth = count($points[0]);
		$pieceHeight = count($points);

		$width = ($boardWidth - $pieceWidth) + 1; // +1 for fence posts
		$height = ($boardHeight - $pieceHeight) + 1; // +1 for fence posts

		// this is like O(log(n)^inf)... sorry about that :(
		// but check out those indents...
		// it's turtles all the way down
		for ($y = 0; $y < $height; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				$boardNodes = array( );

				for ($py = 0; $py < $pieceHeight; ++$py) {
					for ($px = 0; $px < $pieceWidth; ++$px) {
						if (1 === $points[$py][$px]) {
							if (0 === $this->layout[$y + $py][$x + $px]) {
								// the piece doesn't fit here, move along...
								continue 3;
							}

							$boardNodes[] = ($x + $px) + (($y + $py) * $boardWidth);
						}
					}
				}

				$nodes[] = $this->createNodeRow($pieceName, $boardNodes);
			}
		}
	}

	/**
	 * @param string $pieceName
	 * @param array $boardNodes
	 *
	 * @return array
	 */
	protected function createNodeRow($pieceName, $boardNodes) {
		$pieceCount = count(self::$PIECES);
		$index = array_search($pieceName, array_keys(self::$PIECES));

		$row = array_fill(0, count($this->colNames) - 1, 0);
		$row[$index] = 1;

		// fill the board nodes
		foreach ($boardNodes as $node) {
			$row[$node + $pieceCount] = 1;
		}

		return $row;
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
		foreach (self::$PIECES as $pieceName => $piece) {
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
				if ( ! $reflected && $piece[self::PIECE_REFLECTION]) {
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
