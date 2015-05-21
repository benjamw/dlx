<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

/**
 * Class Polyominoes
 * An abstract class that can be extended to solve
 * flat 2D polyomino puzzles with various shaped pieces and boards
 *
 * @see http://en.wikipedia.org/wiki/Polyomino
 * @package DLX\Puzzles
 */
abstract class Polyominoes
{

	/**
	 * $PIECES array indexes
	 */
	const PIECE_COUNT = 0;
	const PIECE_REFLECT = 1;
	const PIECE_SYMMETRY = 2;
	const PIECE_POINTS = 3;

	/**
	 * To be filled by child classes...
	 *
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points should be oriented to put a 1 value in the NW corner
	 *
	 * @var array
	 */
	public static $PIECES = array( );

	/**
	 * @var array
	 */
	public $layout;

	/**
	 * The layout index to column index translation array
	 *
	 * @var array
	 */
	public $translate;

	/**
	 * @var \DLX\Grid
	 */
	public $grid;

	/**
	 * @var array
	 */
	public $colNames;

	/**
	 * Set to true to disregard rotated and reflected solutions
	 *
	 * @var bool
	 */
	public $symmetry;

	/**
	 * The total space size of the layout
	 *
	 * @var int
	 */
	protected $size = 0;

	/**
	 * @var int
	 */
	protected $pieceCount = 0;


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 * @return Polyominoes
	 */
	public function __construct($cols = 0, $rows = 0, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;
			$cols = $rows;
			$rows = $symmetry;
			$symmetry = $temp;
		}
		elseif (is_bool($rows)) {
			// $cols was a full layout
			$symmetry = $rows;
			$rows = 6;
		}

		$this->symmetry = $symmetry;

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
	 * @param null $layers [not used]
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function createLayout($cols, $rows, $layers = null) {
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

			if ($this->size !== $count) {
				throw new Exception('Invalid layout size');
			}

			$this->layout = $cols;
		}
		else {
			$this->layout = array( );

			if ($this->size !== ($cols * $rows)) {
				throw new Exception('Invalid layout size');
			}

			for ($i = 0; $i < $rows; ++$i) {
				$this->layout[] = array_fill(0, $cols, 1);
			}
		}

		// because the layout may have holes in it, or other odd shapes,
		// the grid indexes may not line up with the actual layout indexes
		// so a translation array is needed
		$this->translate = self::createTranslationArray($this->layout);
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
	 * Manually place pieces
	 *
	 * @param array $pieces (not sure of the best syntax to use here...)
	 *
	 * @throws Exception
	 * @return void
	 */
	public function place($pieces) {
		if (1 !== func_num_args( )) {
			$pieces = func_get_args( );
		}

		if ( ! is_array($pieces) || ! is_array(reset($pieces))) {
			$pieces = array($pieces);
		}

		// convert pieces to cols
		$cols = array( );
		foreach ($pieces as $idx => $piece) {
			foreach ($piece as $col) {
				$cols[$idx][] = array_search($col, $this->colNames);
			}
		}

		try {
			$this->grid->selectCols($cols);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Prevent the solution from containing certain pieces in certain locations
	 *
	 * @param $pieces
	 *
	 * @throws Exception
	 * @return void
	 */
	public function exclude($pieces) {
		if (1 !== func_num_args( )) {
			$pieces = func_get_args( );
		}

		if ( ! array_key_exists(0, $pieces)) {
			$pieces = array($pieces);
		}

		// convert pieces to cols
		$cols = array( );
		foreach ($pieces as $idx => $piece) {
			foreach ($piece as $col) {
				$cols[$idx][] = array_search($col, $this->colNames);
			}
		}

		try {
			$this->grid->excludeCols($cols);
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

		foreach ($this->layout as $row => $cols) {
			foreach ($cols as $col => $value) {
				if ( ! $value) {
					continue;
				}

				$colNames[] = "[". ($col + 1) .",". ($row + 1) ."]";
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
	protected function createPieceNodes($pieceName, $piece, & $nodes, $fixed = false) {
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
			if ( ! $reflected && ! $fixed && $piece[self::PIECE_REFLECT]) {
				$points = self::reflectPiece($piece[self::PIECE_POINTS]);
				$reflected = true;
				$done = false;
			}
		}
	}

	/**
	 * If the callback returns false, the solutions will not be stored in Grid
	 *
	 * @param int $count optional solutions to return (0 to return all)
	 * @param callable $callback optional function
	 *
	 * @return array
	 */
	public function solve($count = 0, $callback = null) {
		$this->grid->search($count, $callback);
		return $this->getSolutions( );
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	public function getSolutions( ) {
		$solutions = $this->grid->getSolutions('cols');

		if ($solutions) {
			$solutions = $this->convertSolutions($solutions);
		}

		return $solutions;
	}

	/**
	 * Convert the solutions to a human readable format
	 *
	 * @param array $solutions
	 *
	 * @return array
	 */
	public function convertSolutions($solutions) {
		if (array_key_exists('cols', $solutions)) {
			$solutions = $solutions['cols'];
		}

		if ( ! is_array($solutions[0][0])) {
			$solutions = array($solutions);
		}

		foreach ($solutions as & $solution) { // mind the reference
			sort($solution);

			foreach ($solution as & $path) { // mind the reference
				sort($path);

				foreach ($path as & $col) { // mind the reference
					$col = $this->colNames[$col];
				}
				unset($col); // kill the reference
			}
			unset($path); // kill the reference
		}
		unset($solution); // kill the reference

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
		$lines = explode("\n", trim($string));

		// make sure every line begins and ends with a dot ( . )
		// and find the longest line
		$len = 0;
		$dots = true;
		foreach ($lines as & $line) { // mind the reference
			$line = trim($line);

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
		unset($line); // kill the reference

		$len -= 2; // subtract two because dots

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

			$n = 0;
			$chars = str_split($line);
			foreach ($chars as $jdx => $char) {
				if ((0 === $jdx) || ($jdx === (count($lines) - 1))) {
					continue;
				}

				if ('*' === $char) {
					$row[$n] = 1;
				}

				++$n;
			}

			$layout[] = $row;
		}

		return $layout;
	}

	/**
	 * @param $layout
	 *
	 * @return array
	 */
	public static function createTranslationArray($layout) {
		$n = 0;
		$translate = array( );
		foreach ($layout as $y => $row) {
			$len = count($row);

			foreach ($row as $x => $space) {
				if (1 === $space) {
					$translate[($y * $len) + $x] = $n;
					++$n;
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

			case -180 : // no break
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
			// the piece is too tall to fit
			// don't fail, just don't place this piece
			return;
		}

		if (count($this->layout[0]) < count($points[0])) {
			// the piece is too long to fit
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

							$boardNodes[] = $this->translate[($x + $px) + (($y + $py) * $boardWidth)];
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
		$pieceCount = $this->getPieceCount( );

		$index = array_search($pieceName, $this->colNames) - 1;

		$row = array_fill(0, count($this->colNames) - 1, 0);
		$row[$index] = 1;

		// fill the board nodes
		foreach ($boardNodes as $node) {
			$row[$node + $pieceCount] = 1;
		}

		return $row;
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getPieceCount( ) {
		if (empty($this->pieceCount)) {
			$this->pieceCount = 0;
			foreach (static::$PIECES as $piece) {
				$this->pieceCount += $piece[self::PIECE_COUNT];
			}
		}

		return $this->pieceCount;
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
