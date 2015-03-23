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
	 *
	 * @var array
	 */
	public static $PIECES = array(
		'F' => array(true, 4, array( )),
		'I' => array(false, 2, array( )),
		'L' => array(true, 4, array( )),
		'N' => array(true, 4, array( )),
		'P' => array(true, 4, array( )),
		'T' => array(false, 4, array( )),
		'U' => array(false, 4, array( )),
		'V' => array(false, 4, array( )),
		'W' => array(false, 4, array( )),
		'X' => array(false, 1, array( )),
		'Y' => array(true, 4, array( )),
		'Z' => array(true, 2, array( )),
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
	 * @param int $rows optional
	 * @param int $cols optional
	 *
	 * @throws Exception
	 * @return Pentominoes
	 */
	public function __construct($rows = 6, $cols = 10) {
		if (is_array($rows)) {
			$this->layout = $rows;
		}
		else {
			$this->layout = array( );

			for ($i = 0; $i < $cols; ++$i) {
				$this->layout[] = array_fill(0, $rows, 1);
			}
		}

		try {
			$this->createGrid( );
		}
		catch (Exception $e) {
			throw $e;
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

}
