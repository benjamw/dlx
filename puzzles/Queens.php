<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

class Queens {

	/**
	 * @var int
	 */
	public $size;

	/**
	 * @var \DLX\Grid
	 */
	public $grid;

	/**
	 * @var array
	 */
	public $rowNames;


	/**
	 * @param int $size
	 *
	 * @throws Exception
	 * @return Queens
	 */
	public function __construct($size = 8) {
		$this->size = (int) $size;

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
		$this->createRowNames( );
		$nodes = $this->createNodes( );

		try {
			// 6N - 6; because 1N for each row and col, then 2N - 3 for each diagonal with single ends removed
			$this->grid = new Grid($nodes, (6 * $this->size) - 6, (4 * $this->size) - 6);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Create the row names used to translate the solutions
	 *
	 * @param void
	 *
	 * @return void
	 */
	protected function createRowNames( ) {
		$rowNames = array(''); // rows are 1-index
		$len = strlen((string) $this->size);

		if (26 > $this->size) {
			for ($n = 0; $n < ($this->size * $this->size); ++$n) {
				$rowNames[] = chr(65 + floor($n / $this->size)) . str_pad(($n % $this->size) + 1, $len, '0', STR_PAD_LEFT);
			}
		}
		else {
			for ($n = 0; $n < ($this->size * $this->size); ++$n) {
				$rowNames[] = 'R'.str_pad(floor($n / $this->size) + 1, $len, '0', STR_PAD_LEFT) . 'F'.str_pad(($n % $this->size) + 1, $len, '0', STR_PAD_LEFT);
			}
		}

		$this->rowNames = $rowNames;
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	protected function createNodes( ) {
		$nodes = array( );

		for ($i = 0; $i < $this->size; ++$i) {
			for ($j = 0; $j < $this->size; ++$j) {
				// fill an empty row
				$row = array(
					array_fill(0, $this->size, 0),
					array_fill(0, $this->size, 0),
					array_fill(0, (2 * $this->size) - 1, 0),
					array_fill(0, (2 * $this->size) - 1, 0),
				);

				$row[0][$i] = 1; // rank
				$row[1][$j] = 1; // file
				$row[2][$i + $j] = 1; // diagonal
				$row[3][$this->size - 1 - $i + $j] = 1; // other diagonal

				// strip off the ends of the diagonals because only one piece touches those
				$row[2] = array_slice($row[2], 1, (2 * $this->size) - 3);
				$row[3] = array_slice($row[3], 1, (2 * $this->size) - 3);

				// flatten the 2D array
				$nodes[] = call_user_func_array('array_merge', $row);
			}
		}

		return $nodes;
	}

	/**
	 * Manually place queens
	 *
	 * @param array $pieces array of board locations (A2, B4, etc.)
	 *
	 * @throws Exception
	 * @return void
	 */
	public function place($pieces) {
		// convert pieces to rows
		$rows = array( );
		foreach ($pieces as $piece) {
			$rows[] = array_search($piece, $this->rowNames);
		}

		try {
			$this->grid->select($rows);
		}
		catch (Exception $e) {
			throw $e;
		}
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
		$solutions = $this->grid->getSolutions('rows');

		foreach ($solutions as & $solution) {
			sort($solution);

			foreach ($solution as & $path) {
				$path = $this->rowNames[$path];
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
