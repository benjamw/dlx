<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

class Pentominoes
{

	/**
	 * This uses the FILNPTUVWXYZ naming scheme
	 *
	 * array(mirror, symmetry, points array)
	 *     array(array(x, y), ...)
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'F' => array( true, 4, array(array(1, 0), array(2, 0), array(0, 1), array(1, 1), array(1, 2))),
		'I' => array(false, 2, array(array(0, 0), array(0, 1), array(0, 2), array(0, 3), array(0, 4))),
		'L' => array( true, 4, array(array(0, 0), array(0, 1), array(0, 2), array(0, 3), array(1, 3))),
		'N' => array( true, 4, array(array(0, 0), array(1, 0), array(1, 1), array(2, 1), array(3, 1))),
		'P' => array( true, 4, array(array(0, 0), array(1, 0), array(0, 1), array(1, 1), array(0, 2))),
		'T' => array(false, 4, array(array(0, 0), array(1, 0), array(2, 0), array(1, 1), array(1, 2))),
		'U' => array(false, 4, array(array(0, 0), array(2, 0), array(0, 1), array(1, 1), array(2, 1))),
		'V' => array(false, 4, array(array(0, 0), array(0, 1), array(0, 2), array(1, 2), array(2, 2))),
		'W' => array(false, 4, array(array(0, 0), array(0, 1), array(1, 1), array(1, 2), array(2, 2))),
		'X' => array(false, 1, array(array(1, 0), array(0, 1), array(1, 1), array(2, 1), array(1, 2))),
		'Y' => array( true, 4, array(array(0, 0), array(0, 1), array(1, 1), array(0, 2), array(0, 3))),
		'Z' => array( true, 2, array(array(0, 0), array(1, 0), array(1, 1), array(1, 2), array(2, 2))),
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
	 * @param string|array|int $rows optional
	 * @param int $cols optional
	 *
	 * @throws Exception
	 * @return Pentominoes
	 */
	public function __construct($rows = 6, $cols = 10) {
		try {
			$this->createLayout($rows, $cols);
			$this->createGrid( );
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * The layout can be passed as a string or a 2D array into $rows
	 * or as row and column dimensions
	 *
	 * If a layout is passed as an array, the indexes for the array
	 * should be valid coordinates for the board. '0' is allowed to
	 * block out certain positions without having to break the array.
	 *
	 * @param string|array|int $rows
	 * @param int $cols
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function createLayout($rows, $cols) {
		if (is_string($rows)) {
			$this->layout = self::layoutToArray($rows);
		}
		elseif (is_array($rows)) {
			$count = 0;

			// this is a simple dimensions test
			// this does not take into account the checkerboard validity of the layout
			foreach ($rows as $row) {
				foreach ($row as $node) {
					if (1 === $node) {
						++$count;
					}
				}
			}

			if (60 !== $count) {
				throw new Exception('Invalid layout size');
			}

			$this->layout = $rows;
		}
		else {
			$this->layout = array( );

			if (60 !== ($rows * $cols)) {
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
			$this->grid = new Grid($nodes, count($this->colNames));
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

// TODO: build this...
// there needs to be a column for each piece, and a column for each position on the board

		$this->colNames = $colNames;
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	protected function createNodes( ) {
		$nodes = array( );

// TODO: build this...
// there needs to be a column for each piece, and a column for each position on the board
// and a row for each piece in each position on the board, and each rotation and mirror

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
	 * @param $array
	 *
	 * @return void
	 */
	public function printArray($array) {
		$array = array_chunk($array, $this->size);
		foreach ($array as & $row) {
			$row = implode(' ', $row);
		}

		echo '<pre>'.implode("\n", $array).'</pre>';
	}

	/**
	 * Convert a layout given in string format
	 * to an array that the class can use
	 *
	 * @param $string
	 *
	 * @return array
	 */
	public static function layoutToArray($string) {
		$layout = array( );

		// TODO: build this

		return $layout;
	}

}
