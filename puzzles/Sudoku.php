<?php

namespace DLX\Puzzles;

use \DLX\Grid;
use \Exception;

/**
 * Class Sudoku
 *
 * This class will solve a square sudoku puzzle
 *
 * @see http://en.wikipedia.org/wiki/Sudoku
 * @package DLX\Puzzles
 */
class Sudoku {

	/**
	 * The possible digits used in the puzzle
	 *
	 * @var string
	 */
	const DIGITS = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
	 * @return Sudoku
	 */
	public function __construct($size = 9) {
		$this->size = (int) $size;

		if ($this->size > 36) {
			throw new Exception('The size given is too large');
		}

		$root = sqrt($this->size);
		if (floor($root) !== $root) {
			throw new Exception('The size given is not a square');
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
		$this->createRowNames( );
		$nodes = $this->createNodes( );

		try {
			$this->grid = new Grid($nodes, count($nodes[0]));
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
		$length = strlen((string) $this->size);

		$rowNames = array(''); // rows are 1-index
		for ($r = 1; $r <= $this->size; ++$r) {
			$row = str_pad($r, $length, '0', STR_PAD_LEFT);

			for ($c = 1; $c <= $this->size; ++$c) {
				$col = str_pad($c, $length, '0', STR_PAD_LEFT);

				for ($d = 0; $d < $this->size; ++$d) {
					$digit = substr(self::DIGITS, $d, 1);
					$rowNames[] = "r{$row}c{$col}#{$digit}";
				}
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

		$root = floor(sqrt($this->size));

		// a column for each space (size * size)
		// for each digit in each row (size * size)
		// for each digit in each column (size * size)
		// and for each digit in each block (size * size)
		$square = ($this->size * $this->size);
		$numCols = $square * 4;

		// the starting indexes for each section
		$spaceStart = $square * 0;
		$rowStart = $square * 1;
		$colStart = $square * 2;
		$blockStart = $square * 3;

		// a row for each digit in each space
		$numRows = $square * $this->size;

		$digit = $spaceIdx = $rowIdx = $rowIdxSize = $colIdx = $colIdxSize = 0;
		$blockColIdx = $blockRowIdx = $blockIdx = $blockIdxSize = 0;
		for ($n = 0; $n < $numRows; ++$n) {
			$row = array_fill(0, $numCols, '');

			// fill the space
			$row[$spaceIdx + $spaceStart] = 1;

			// fill the row with this digit
			$row[$rowIdxSize + $digit + $rowStart] = 1;

			// fill the column with this digit
			$row[$colIdxSize + $digit + $colStart] = 1;

			// fill the block with the digit
			$row[$blockIdxSize + $digit + $blockStart] = 1;

			$nodes[] = $row;

			++$digit;

			if ($digit === $this->size) {
				$digit = 0;
				++$colIdx;

				if ($colIdx === $this->size) {
					$colIdx = 0;
					++$rowIdx;

					if ($rowIdx === $this->size) {
						continue; // this should end the loop, because should be done
					}

					$rowIdxSize = $rowIdx * $this->size;
					$blockRowIdx = floor($rowIdx / $root);
				}

				$colIdxSize = $colIdx * $this->size;
				$spaceIdx = $colIdx + ($rowIdx * $this->size);

				$blockColIdx = floor($colIdx / $root);
				$blockIdx = $blockColIdx + ($blockRowIdx * $root);
				$blockIdxSize = $blockIdx * $this->size;
			}
		}

		return $nodes;
	}

	/**
	 * Manually fill puzzle
	 * The syntax is like 4..6.8..2..7...5...1..5..7.2..3....9..9...3..8....9..4.9..1..3...2...9..3..8.5..6
	 *
	 *     or
	 *
	 *     4 . . | 6 . 8 | . . 2
	 *     . . 7 | . . . | 5 . .
	 *     . 1 . | . 5 . | . 7 .
	 *     ------+-------+------
	 *     2 . . | 3 . . | . . 9
	 *     . . 9 | . . . | 3 . .
	 *     8 . . | . . 9 | . . 4
	 *     ------+-------+------
	 *     . 9 . | . 1 . | . 3 .
	 *     . . 2 | . . . | 9 . .
	 *     3 . . | 8 . 5 | . . 6
	 *
	 * Whitespace/separation characters can be included and anything
	 * not in [a-z0-9.] is stripped before processing
	 *
	 * @param string $puzzle string of digits in pseudo-standard sudoku string syntax
	 *
	 * @throws Exception
	 * @return void
	 */
	public function fill($puzzle) {
		$puzzle = preg_replace('%[^a-z0-9.]+%i', '', $puzzle);
		$puzzle = str_split(strtoupper($puzzle));

		if (count($puzzle) !== ($this->size * $this->size)) {
			throw new Exception ('Given puzzle is incorrect size');
		}

		$length = strlen((string) $this->size);

		// convert puzzle to rows
		$rows = array( );
		foreach ($puzzle as $idx => $digit) {
			if ('.' === $digit) {
				continue;
			}

			$col = ($idx % $this->size) + 1;
			$col = str_pad($col, $length, '0', STR_PAD_LEFT);
			$row = floor($idx / $this->size) + 1;
			$row = str_pad($row, $length, '0', STR_PAD_LEFT);

			$rowName = "r{$row}c{$col}#{$digit}";

			$rows[] = array_search($rowName, $this->rowNames);
		}

		try {
			$this->grid->selectRows($rows);
		}
		catch (Exception $e) {
			throw $e;
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
	 * @param bool $format optionally format the solutions into a grid string
	 *
	 * @return array
	 */
	public function getSolutions($format = false) {
		$solutions = $this->grid->getSolutions('rows');
		$solutions = $this->convertSolutions($solutions, $format);

		if (1 === count($solutions)) {
			$solutions = reset($solutions);
		}

		return $solutions;
	}

	/**
	 * Convert the solutions to a human readable format
	 *
	 * @param array $solutions
	 * @param bool $format optionally format the solutions into a grid string
	 *
	 * @return array
	 */
	public function convertSolutions($solutions, $format = false) {
		if (array_key_exists('rows', $solutions)) {
			$solutions = $solutions['rows'];
		}

		foreach ($solutions as & $solution) { // mind the reference
			sort($solution);

			foreach ($solution as & $path) { // mind the reference
				$path = $this->rowNames[$path];

				if ($format) {
					$path = substr($path, strpos($path, '#') + 1);
				}
			}
			unset($path); // kill the reference

			if ($format) {
				$solution = array_chunk($solution, $this->size);
				array_walk($solution, function (& $v) { $v = implode(' ', $v); });
				$solution = implode("\n", $solution);
			}
		}
		unset($solution); // kill the reference

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
