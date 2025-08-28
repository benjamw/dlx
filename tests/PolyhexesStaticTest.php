<?php

namespace DLX\Tests;

// call these by using
// $> vendor/bin/phpunit --coverage-html .\report tests\PolyhexesStaticTest.php
// or
// $> vendor/bin/phpunit UnitTest tests\PolyhexesStaticTest.php
// from the DLX directory

use \DLX\Puzzles\Polyhexes;
use Exception;
use ReflectionClass;
use ReflectionException;

class PolyhexesStaticTest extends \PHPUnit\Framework\TestCase {

	public function testRotatePiece0() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$returned = Polyhexes::rotatePiece(0, $piece);

		$this->assertEquals($piece, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(0, $piece);

		$this->assertEquals($piece, $returned);
	}

	public function testRotatePiece60() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 1],
			  [0, 1, 0],
			    [0, 1, 0],
			      [1, 1, 0],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 1],
			  [0, 1],
			    [1, 1],
			      [0, 1],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceNeg60() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 0, 1],
			  [0, 0, 1, 1],
			    [1, 1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(-60, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 0, 0, 1],
			  [0, 0, 1, 0],
			    [0, 1, 1, 0],
			      [1, 0, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(-60, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece120() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 1, 1],
			  [1, 1, 0, 0],
			    [1, 0, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(120, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 0, 0, 1],
			  [0, 1, 1, 0],
			    [0, 1, 0, 0],
			      [1, 0, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(120, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceNeg120() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 1, 1],
			  [0, 1, 0],
			    [0, 1, 0],
			      [1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(-120, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[1, 0],
			  [1, 1],
			    [1, 0],
			      [1, 0],
		];

		$returned = Polyhexes::rotatePiece(-120, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece180() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 1, 0],
			  [1, 1, 1],
			    [0, 0, 1],
		];

		$returned = Polyhexes::rotatePiece(180, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 0, 1, 0],
			  [1, 1, 1, 1],
		];

		$returned = Polyhexes::rotatePiece(180, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceNeg180() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 1, 0],
			  [1, 1, 1],
			    [0, 0, 1],
		];

		$returned = Polyhexes::rotatePiece(-180, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 0, 1, 0],
			  [1, 1, 1, 1],
		];

		$returned = Polyhexes::rotatePiece(-180, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece240() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 1, 1],
			  [0, 1, 0],
			    [0, 1, 0],
			      [1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(240, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[1, 0],
			  [1, 1],
			    [1, 0],
			      [1, 0],
		];

		$returned = Polyhexes::rotatePiece(240, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece300() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 0, 1],
			  [0, 0, 1, 1],
			    [1, 1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(300, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[0, 0, 0, 1],
			  [0, 0, 1, 0],
			    [0, 1, 1, 0],
			      [1, 0, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(300, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece360() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$returned = Polyhexes::rotatePiece(360, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$expected = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		$returned = Polyhexes::rotatePiece(360, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceSymmetric120() {
		$piece = [
			[0, 1],
			  [1, 1],
		];

		$returned = Polyhexes::rotatePiece(120, $piece);

		$this->assertEquals($piece, $returned);
	}

	public function testRotatePieceSymmetric60() {
		$piece = [
			[0, 1, 1],
			  [1, 0, 1],
			    [1, 1, 0],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($piece, $returned);
	}

	public function testRotatePieceMultiple60() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		// Rotate 60 degrees 6 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 6; $i++) {
			$rotated = Polyhexes::rotatePiece(60, $rotated);
		}

		$this->assertEquals($piece, $rotated);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		// Rotate 60 degrees 6 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 6; $i++) {
			$rotated = Polyhexes::rotatePiece(60, $rotated);
		}

		$this->assertEquals($piece, $rotated);
	}

	public function testRotatePieceMultiple120() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		// Rotate 120 degrees 3 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 3; $i++) {
			$rotated = Polyhexes::rotatePiece(120, $rotated);
		}

		$this->assertEquals($piece, $rotated);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		// Rotate 120 degrees 3 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 3; $i++) {
			$rotated = Polyhexes::rotatePiece(120, $rotated);
		}

		$this->assertEquals($piece, $rotated);
	}

	public function testRotatePieceMultiple180() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		// Rotate 180 degrees 2 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 2; $i++) {
			$rotated = Polyhexes::rotatePiece(180, $rotated);
		}

		$this->assertEquals($piece, $rotated);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1, 0, 0],
		];

		// Rotate 180 degrees 2 times should return to original
		$rotated = $piece;
		for ($i = 0; $i < 2; $i++) {
			$rotated = Polyhexes::rotatePiece(180, $rotated);
		}

		$this->assertEquals($piece, $rotated);
	}

	public function testRotatePieceSingleCell() {
		$piece = [
			[1],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($piece, $returned);
	}

	public function testRotatePieceIrregularShape() {
		$piece = [
			[1],
			  [1, 1, 1],
			    [0, 1],
		];

		$expected = [
			[0, 0, 1],
			  [0, 1, 0],
			    [0, 1, 0],
			      [1, 1, 0],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1, 1],
			  [0, 1],
		];

		$expected = [
			[0, 1],
			  [0, 1],
			    [1, 1],
			      [0, 1],
		];

		$returned = Polyhexes::rotatePiece(60, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceInvalidDegree() {
		$this->expectException(Exception::class);
		$piece = [
			[1, 1],
			[1, 1],
		];

		Polyhexes::rotatePiece(45, $piece);
	}

	public function testRotatePieceInvalidDegree2() {
		$this->expectException(Exception::class);
		$piece = [
			[1, 1],
			[1, 1],
		];

		Polyhexes::rotatePiece(90, $piece);
	}

	public function testRotatePieceInvalidDegree3() {
		$this->expectException(Exception::class);
		$piece = [
			[1, 1],
			[1, 1],
		];

		Polyhexes::rotatePiece(270, $piece);
	}

	public function testReflectPiece() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 1],
			  [0, 0, 1],
			    [0, 1, 0],
			      [1, 1, 0],
		];

		$returned = Polyhexes::reflectPiece($piece);

		$this->assertEquals($expected, $returned);

		// ---------------------------------------------------------

		$piece = [
			[1, 1, 1],
			  [1, 0, 1],
		];

		$expected = [
			[0, 0, 1],
			  [0, 1, 1],
			    [1, 0, 0],
			      [1, 0, 0],
		];

		$returned = Polyhexes::reflectPiece($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testLayoutToArray() {
		$string = "
			. . . . . . . .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * . . . .
			. * * * . . . .
			. * * * . . . .
			. . . . . . . .
		";

		$expected = [
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1],
			[1, 1, 1, 0, 0, 0],
			[1, 1, 1, 0, 0, 0],
			[1, 1, 1, 0, 0, 0],
		];

		$returned = Polyhexes::layoutToArray($string);

		$this->assertEquals($expected, $returned);
	}

	public function testLayoutToArrayHexagonal() {
		$string = "
			. . . . . . . . . .
			. . * * * * * * . .
			. * * * * * * * * .
			. * * * * * * * * .
			. * * * * * * * * .
			. * * * * * * * * .
			. . * * * * * * . .
			. . . . . . . . . .
		";

		$expected = [
			[0, 1, 1, 1, 1, 1, 1, 0],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[0, 1, 1, 1, 1, 1, 1, 0],
		];

		$returned = Polyhexes::layoutToArray($string);

		$this->assertEquals($expected, $returned);
	}

	public function testCreateTranslationArray() {
		$layout = [
			[1, 1, 0],
			[0, 1, 1],
			[1, 0, 1],
		];

		$expected = [
			0 => 0,  // (0,0)
			1 => 1,  // (1,0)
			4 => 2,  // (1,1)
			5 => 3,  // (2,1)
			6 => 4,  // (0,2)
			8 => 5,  // (2,2)
		];

		$returned = Polyhexes::createTranslationArray($layout);

		$this->assertEquals($expected, $returned);
	}

	public function testCreateTranslationArrayWithHoles() {
		$layout = [
			[1, 0, 1],
			[0, 0, 0],
			[1, 0, 1],
		];

		$expected = [
			0 => 0,  // (0,0)
			2 => 1,  // (2,0)
			6 => 2,  // (0,2)
			8 => 3,  // (2,2)
		];

		$returned = Polyhexes::createTranslationArray($layout);

		$this->assertEquals($expected, $returned);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testTrimArray() {
		$array = [
			[0, 0, 0, 0],
			[0, 1, 1, 0],
			[0, 1, 1, 0],
			[0, 0, 0, 0],
		];

		$expected = [
			[1, 1],
			[1, 1],
		];

		// Use reflection to access protected method
		$reflection = new ReflectionClass('DLX\Puzzles\Polyhexes');
		$method = $reflection->getMethod('trimArray');
		$method->setAccessible(true);

		$returned = $method->invoke(null, $array);

		$this->assertEquals($expected, $returned);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testTrimArrayIrregular() {
		$array = [
			[0, 0, 0, 0, 0],
			[0, 0, 1, 0, 0],
			[0, 1, 1, 1, 0],
			[0, 0, 1, 0, 0],
			[0, 0, 0, 0, 0],
		];

		$expected = [
			[0, 1, 0],
			[1, 1, 1],
			[0, 1, 0],
		];

		// Use reflection to access protected method
		$reflection = new ReflectionClass('DLX\Puzzles\Polyhexes');
		$method = $reflection->getMethod('trimArray');
		$method->setAccessible(true);

		$returned = $method->invoke(null, $array);

		$this->assertEquals($expected, $returned);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testTrimArrayNegative() {
		$array = [
			-5 => [-2 => 0, -1 => 0, 0 => 0, 1 => 0, 2 => 0],
			-4 => [-2 => 0, -1 => 0, 0 => 1, 1 => 0, 2 => 0],
			-3 => [-2 => 0, -1 => 1, 0 => 1, 1 => 1, 2 => 0],
			-2 => [-2 => 0, -1 => 0, 0 => 1, 1 => 0, 2 => 0],
			-1 => [-2 => 0, -1 => 0, 0 => 0, 1 => 0, 2 => 0],
		];

		$expected = [
			[0, 1, 0],
			[1, 1, 1],
			[0, 1, 0],
		];

		// Use reflection to access protected method
		$reflection = new ReflectionClass('DLX\Puzzles\Polyhexes');
		$method = $reflection->getMethod('trimArray');
		$method->setAccessible(true);

		$returned = $method->invoke(null, $array);

		$this->assertEquals($expected, $returned);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testRotatePiece60Method() {
		$piece = [
			[1, 0, 0],
			  [1, 1, 1],
			    [0, 1, 0],
		];

		$expected = [
			[0, 0, 1],
			  [0, 1, 0],
			    [0, 1, 0],
			      [1, 1, 0],
		];

		// Use reflection to access protected method
		$reflection = new ReflectionClass('DLX\Puzzles\Polyhexes');
		$method = $reflection->getMethod('rotatePiece60');
		$method->setAccessible(true);

		$returned = $method->invoke(null, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testArrayToCubicCoordinatesStandard() {
		$piece = [
			[1, 1, 1],
			  [1, 0, 1],
			    [1, 1, 1],
		];

		$expected = [
			[0, 0, 0],
			[1, 0, -1],
			[2, 0, -2],

			[0, 1, -1],
			// zero is here
			[2, 1, -3],

			[0, 2, -2],
			[1, 2, -3],
			[2, 2, -4],
		];

		$returned = Polyhexes::arrayToCubicCoordinates($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testArrayToCubicCoordinatesIrregular() {
		$piece = [
			[1, 1],
			  [1, 0, 1, 1],
			    [1, 0, 1, 0],
		];

		$expected = [
			[0, 0, 0],
			[1, 0, -1],

			[0, 1, -1],
			// zero is here
			[2, 1, -3],
			[3, 1, -4],

			[0, 2, -2],
			// zero is here
			[2, 2, -4],
			// zero is here
		];

		$returned = Polyhexes::arrayToCubicCoordinates($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testArrayToCubicCoordinatesEmpty() {
		$piece = [];

		$expected = [];

		$returned = Polyhexes::arrayToCubicCoordinates($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testArrayToCubicCoordinatesZeros() {
		$piece = [
			[0, 0, 0],
			  [0, 0, 0],
			    [0, 0, 0],
		];

		$expected = [];

		$returned = Polyhexes::arrayToCubicCoordinates($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testArrayToCubicCoordinatesSingle() {
		$piece = [
			[1],
		];

		$expected = [
			[0, 0, 0],
		];

		$returned = Polyhexes::arrayToCubicCoordinates($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testCubicCoordinatesToArray() {
		$cubicCoords = [
			[0, 0, 0],
			[1, 0, -1],
			[2, 0, -2],

			[0, 1, -1],
			// zero is here
			[2, 1, -3],

			[0, 2, -2],
			[1, 2, -3],
			[2, 2, -4],
		];

		$expected = [
			[1, 1, 1],
			  [1, 0, 1],
			    [1, 1, 1],
		];

		$returned = Polyhexes::cubicCoordinatesToArray($cubicCoords);

		$this->assertEquals($expected, $returned);
	}

	public function testCubicCoordinatesToArrayIrregular() {
		$cubicCoords = [
			[0, 0, 0],
			[1, 0, -1],

			[0, 1, -1],
			// zero is here
			[2, 1, -3],
			[3, 1, -4],

			[0, 2, -2],
			// zero is here
			[2, 2, -4],
			// zero is here
		];

		// note that the expected array is not "irregular" like the 
		// related array from the testArrayToCubicCoordinatesIrregular() test
		$expected = [
			[1, 1, 0, 0],
			  [1, 0, 1, 1],
			    [1, 0, 1, 0],
		];

		$returned = Polyhexes::cubicCoordinatesToArray($cubicCoords);

		$this->assertEquals($expected, $returned);
	}

	public function testCubicCoordinatesToArrayEmpty() {
		$cubicCoords = [];

		$expected = [];

		$returned = Polyhexes::cubicCoordinatesToArray($cubicCoords);

		$this->assertEquals($expected, $returned);
	}

	public function testCubicCoordinatesToArraySingle() {
		$cubicCoords = [
			[0, 0, 0],
		];

		$expected = [
			[1],
		];

		$returned = Polyhexes::cubicCoordinatesToArray($cubicCoords);

		$this->assertEquals($expected, $returned);
	}

	public function testCubicCoordinatesToArrayRoundTrip() {
		$piece = [
			[1, 0, 1],
			  [1, 0, 0],
			    [0, 1, 1],
		];

		$cubicCoords = Polyhexes::arrayToCubicCoordinates($piece);
		$backToArray = Polyhexes::cubicCoordinatesToArray($cubicCoords);

		$this->assertEquals($piece, $backToArray);
	}

}
