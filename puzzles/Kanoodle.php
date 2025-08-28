<?php

namespace DLX\Puzzles;

use \Exception;

/**
 * Class Kanoodle
 * Kanoodle puzzles are a distinct set of polyhex puzzles that come in several form factors
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
	 * [count, mirror, symmetry, points array]
	 *     points are a 2D array of values, 1 = on, 0 = off
	 *     points were oriented to put a 1 value in the NW corner (where possible)
	 *
	 * A-G are used in the Kanoodle Genius game
	 * A-L are used in the Kanoodle Extreme game
	 *
	 * @var array
	 */
	public static array $PIECES = [
		'A' => [1, false, 6, [ // dark blue
			[1, 0, 0],
			  [1, 0, 0],
			    [1, 1, 1],
			]],
		'B' => [1,  true, 6, [ // light green
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
			]],
		'C' => [1,  true, 6, [ // yellow
			[1, 0],
			  [1, 1],
			    [1, 1],
			]],
		'D' => [1,  true, 6, [ // light blue
			[1, 1, 1, 1],
			  [1, 0, 0, 0],
			]],
		'E' => [1, false, 6, [ // red
			[1, 1],
			  [1, 1],
			    [1, 0],
			]],
		'F' => [1, false, 6, [ // magenta
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
			]],
		'G' => [1,  true, 6, [ // dark green
			[1, 1, 1],
			  [1, 0, 1],
			]],


		'H' => [1, false, 6, [ // white
			[1, 0, 1],
			  [1, 1, 0],
			    [1, 0, 0],
			]],
		'I' => [1,  true, 6, [ // orange
			[1, 0],
			  [1, 0],
			    [1, 1],
			]],
		'J' => [1,  true, 3, [ // pink
			[1, 1, 0],
			  [0, 1, 1],
			]],
		'K' => [1, false, 3, [ // gray
			[1, 1],
			  [1, 1],
			]],
		'L' => [1,  true, 6, [ // purple
			[1, 1, 1],
			  [1, 0, 0],
			]],
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

		$cols = [
			[0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
			 [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
			  [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
			   [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
			    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
		];

		- OR -

		$cols = "
			...............
			 ...***********.
			  ..***********..
			   .************..
			    .***********...
			     .***********...
			      ...............
		";

	 	-- for the Kanoodle Genius --

TODO: Fix this by canting it the other way

		$cols = [
			            [1, 1, 1, 1, 0, 0, 0],
			          [1, 1, 1, 1, 1, 0, 0],
			        [1, 1, 1, 1, 1, 1, 0],
			      [0, 1, 1, 1, 1, 1, 0],
			    [0, 1, 1, 1, 1, 1, 1],
			  [0, 0, 1, 1, 1, 1, 1],
			[0, 0, 0, 1, 1, 1, 1],
		];

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

		parent::__construct($cols, $rows, $symmetry);
	}

}
