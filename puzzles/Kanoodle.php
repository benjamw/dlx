<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Kanoodle
 * Kanoodle Extreme puzzles are a distinct set of polyhex puzzles that come in several form factors
 * These puzzles are related to Lonpos puzzles, but are isometric instead of orthogonal
 *
 * This class solves the flat 2D puzzles of various shapes
 *
 * @see http://en.wikipedia.org/wiki/Lonpos
 * @see https://www.educationalinsights.com/product/kanoodle--174+extreme.do
 * @package DLX\Puzzles
 */
class Kanoodle extends Polyhexes
{

	/**
	 * array(count, mirror, symmetry, points array)
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner (where possible)
	 *
	 * A-G are used in the Kanoodle Genius game
	 * A-L are used in the Kanoodle Extreme game
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'A' => [1, false, 6, [[1, 0, 0, 0], [1, 1, 1, 0], [0, 0, 0, 1]]], // light green
		'B' => [1, false, 6, [[1, 1, 1, 0], [0, 0, 1, 1]]], // yellow
		'C' => [1,  true, 6, [[1, 1, 1], [1, 0, 0], [1, 0, 0]]], // dark blue
		'D' => [1, false, 6, [[1, 1, 1, 1], [0, 1, 0, 0]]], // light blue
		'E' => [1,  true, 6, [[1, 1, 1], [0, 1, 1]]], // red
		'F' => [1,  true, 6, [[1, 1, 1, 1], [0, 0, 1, 0]]], // magenta
		'G' => [1, false, 6, [[1, 1, 1], [1, 0, 1]]], // dark green

		'H' => [1,  true, 6, [[1, 1, 1], [0, 1, 0], [0, 0, 1]]], // white
		'I' => [1, false, 6, [[1, 1, 1], [1, 0, 0]]], // orange
		'J' => [1, false, 3, [[1, 1, 0, 0], [0, 0, 1, 1]]], // pink
		'K' => [1,  true, 3, [[1, 1], [1, 1]]], // gray
		'L' => [1, false, 6, [[1, 1, 1], [0, 1, 0]]], // purple
	];

	/**
	 * Kanoodle Genius is 35
	 * Kanoodle Extreme is 56
	 *
	 * @var int
	 */
	protected int $size = 56;

	/**
		The layout to pass in for the Kanoodle Extreme would be the following:

		$cols = array(
			        array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0),
			      array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0),
			    array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
			  array(0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
			array(0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
		);

		- OR -

		$cols = "
			...............
			.***********...
			.***********...
			.************..
			..***********..
			...***********.
			...............
		";

	 	-- for the Kanoodle Genius --

		$cols = array(
			array(1, 1, 1, 1, 0, 0, 0),
			array(1, 1, 1, 1, 1, 0, 0),
			array(1, 1, 1, 1, 1, 1, 0),
			array(0, 1, 1, 1, 1, 1, 0),
			array(0, 1, 1, 1, 1, 1, 1),
			array(0, 0, 1, 1, 1, 1, 1),
			array(0, 0, 0, 1, 1, 1, 1),
		);

		- OR -

		$cols = "
			.........
			.****....
			.*****...
			.******..
			..*****..
			..******.
			...*****.
			....****.
			.........
		";

	 TODO: add more...

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
