<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Lonpos
 * Lonpos puzzles are a distinct set of polyomino puzzles that come in several form factors
 *
 * This class solves the flat 2D puzzles of various shapes
 *
 * @see http://en.wikipedia.org/wiki/Lonpos
 * @package DLX\Puzzles
 */
class Lonpos extends Polyominoes
{

	/**
	 * [count, mirror, symmetry, points array]
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner (where possible)
	 *
	 * @var array
	 */
	public static array $PIECES = [
		// L is first because it's the hardest to place
		'L' => [1, false, 1, [[0, 1, 0], [1, 1, 1], [0, 1, 0]]], // gray X
		'A' => [1,  true, 4, [[1, 1, 1], [1, 0, 0]]], // orange L
		'B' => [1,  true, 4, [[1, 1, 1], [1, 1, 0]]], // red P
		'C' => [1,  true, 4, [[1, 1, 1, 1], [1, 0, 0, 0]]], // dk. blue L
		'D' => [1,  true, 4, [[1, 1, 1, 1], [0, 1, 0, 0]]], // pink Y
		'E' => [1,  true, 4, [[1, 1, 1, 0], [0, 0, 1, 1]]], // green N
		'F' => [1, false, 4, [[1, 0], [1, 1]]], // white V
		'G' => [1, false, 4, [[1, 0, 0], [1, 0, 0], [1, 1, 1]]], // lt. blue V
		'H' => [1, false, 4, [[1, 0, 0], [1, 1, 0], [0, 1, 1]]], // magenta W
		'I' => [1, false, 4, [[1, 1, 1], [1, 0, 1]]], // yellow U
		'J' => [1, false, 2, [[1, 1, 1, 1]]], // purple I
		'K' => [1, false, 1, [[1, 1], [1, 1]]], // lime O
	];

	/**
	 * @var int
	 */
	protected int $size = 55;

	/**
		The layout to pass in for the colorful cabin would be the following:

		$cols = [
			[1, 1, 1, 1, 1, 1, 0, 0, 0],
			[1, 1, 1, 1, 1, 1, 1, 0, 0],
			[1, 1, 1, 1, 1, 1, 1, 1, 0],
			[1, 1, 0, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 0, 0],
			[0, 1, 1, 1, 1, 1, 0, 0, 0],
			[0, 0, 1, 1, 1, 0, 0, 0, 0],
			[0, 0, 0, 1, 1, 0, 0, 0, 0],
		];

		- OR -

		$cols = "
			........
			.******.
			.*******.
			.********.
			.**.******.
			.*********.
			.*******.
			..*****.
			...***.
			....**.
			.......
		";


		and for the crazy cone it would be the following:

		$cols = [
			[1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
			[1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
			[1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
			[1, 1, 1, 1, 1, 1, 0, 0, 0, 0],
			[1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
			[1, 1, 1, 1, 0, 0, 0, 0, 0, 0],
			[1, 1, 1, 0, 0, 0, 0, 0, 0, 0],
			[1, 1, 0, 0, 0, 0, 0, 0, 0, 0],
			[1, 0, 0, 0, 0, 0, 0, 0, 0, 0],
		];

		- OR -

		$cols = "
			............
			.**********.
			.*********.
			.********.
			.*******.
			.******.
			.*****.
			.****.
			.***.
			.**.
			.*.
			..
		";


		and for the crooked square it would be the following:

		$cols = [
			[0, 0, 0, 0, 0, 1, 0, 0, 0, 0],
			[0, 0, 0, 0, 1, 1, 1, 0, 0, 0],
			[0, 0, 0, 1, 1, 1, 1, 1, 0, 0],
			[0, 0, 1, 1, 1, 1, 1, 1, 1, 0],
			[0, 1, 1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
			[0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
			[0, 0, 1, 1, 1, 1, 1, 0, 0, 0],
			[0, 0, 0, 1, 1, 1, 0, 0, 0, 0],
			[0, 0, 0, 0, 1, 0, 0, 0, 0, 0],
		];

		- OR -

		$cols = "
			............
			......*.....
			.....***....
			....*****...
			...*******..
			..*********.
			.*********..
			..*******...
			...*****....
			....***.....
			.....*......
			............
		";

	 **/


	/**
	 * A custom layout can be passed as a 2D array into the first argument
	 *
	 * @param string|array|int $cols optional
	 * @param int $rows optional
	 * @param bool $symmetry optional
	 *
	 * @throws Exception
	 */
	public function __construct($cols = 11, $rows = 5, $symmetry = false) {
		if (is_bool($cols)) {
			$temp = $cols;

			if (5 !== $rows) {
				$cols = $rows;
				$rows = $symmetry;
			}
			else {
				$cols = 11;
			}

			$symmetry = $temp;
		}
		elseif (is_bool($rows)) {
			// $cols was a full layout
			$symmetry = $rows;
			$rows = 5;
		}

		try {
			parent::__construct($cols, $rows, $symmetry);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

}
