<?php

namespace DLX\Tests;

// call these by using
// $> vendor/bin/phpunit --coverage-html .\report tests\PentominoStaticTest.php
// or
// $> vendor/bin/phpunit UnitTest tests\PentominoStaticTest.php
// from the DLX directory

use \DLX\Puzzles\Pentominoes;

class PentominoStaticTest extends \PHPUnit\Framework\TestCase {

	public function testRotatePiece0( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		$returned = Pentominoes::rotatePiece(0, $piece);

		$this->assertEquals($piece, $returned);
	}

	public function testRotatePiece90( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		$expected = [
			[9, 6, 3, 0],
			['a', 7, 4, 1],
			['b', 8, 5, 2],
		];

		$returned = Pentominoes::rotatePiece(90, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePieceNeg90( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		$expected = [
			[2, 5, 8, 'b'],
			[1, 4, 7, 'a'],
			[0, 3, 6, 9],
		];

		$returned = Pentominoes::rotatePiece(-90, $piece);

		$this->assertEquals($expected, $returned);
	}

	public function testRotatePiece180( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		$expected = [
			['b', 'a', 9],
			[8, 7, 6],
			[5, 4, 3],
			[2, 1, 0],
		];

		$returned = Pentominoes::rotatePiece(180, $piece);

		$this->assertEquals($expected, $returned);
	}

	/**
	 * @expectedException Exception
	 */
	public function testRotatePiece170( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		Pentominoes::rotatePiece(170, $piece);
	}

	public function testReflectPiece( ) {
		$piece = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[9, 'a', 'b'],
		];

		$expected = [
			[9, 'a', 'b'],
			[6, 7, 8],
			[3, 4, 5],
			[0, 1, 2],
		];

		$returned = Pentominoes::reflectPiece($piece);

		$this->assertEquals($expected, $returned);
	}

	public function testLayoutToArray( ) {
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

		$returned = Pentominoes::layoutToArray($string);

		$this->assertEquals($expected, $returned);
	}

	public function testLayoutToArray2( ) {
		$string = "
			. . . . . . . .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * * * * .
			. * * * .
			. * * * .
			. * * * .
			. . . . .
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

		$returned = Pentominoes::layoutToArray($string);

		$this->assertEquals($expected, $returned);
	}

	public function testLayoutToArray3( ) {
		$string = "
			. . . . . . . . . .
			. * * * * * * * * .
			. * * * * * * * * .
			. * * . * * . * * .
			. * * * * * * * * .
			. * * * * * * * * .
			. * * . * * . * * .
			. * * * * * * * * .
			. * * * * * * * * .
			. . . . . . . . . .
		";

		$expected = [
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 0, 1, 1, 0, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 0, 1, 1, 0, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
			[1, 1, 1, 1, 1, 1, 1, 1],
		];

		$returned = Pentominoes::layoutToArray($string);

		$this->assertEquals($expected, $returned);
	}



	/**
	 * @expectedException Exception
	 */
	public function test_piece_code_exception( ) {
		Chess::piece_code_to_fen_code(33);
	}


	/**
	 * @expectedException Exception
	 */
	public function test_piece_exception( ) {
		Chess::get_piece_code('purple', 'juggler');
	}


	public function test_pgn_code( ) {
		foreach ($this->pgn_codes as $piece => $exp_code) {
			$code = Chess::get_pgn_code($piece);
			$this->assertEquals($exp_code, $code);

			$code = Chess::get_pgn_code(strtoupper($piece));
			$this->assertEquals($exp_code, $code);
		}
	}


	// this test is by no means exhaustive, yet
	public function test_fen( ) {
		$blank_FEN = '8/8/8/8/8/8/8/8';
		$blank_expanded = str_repeat('0', 64);

		$xFEN = Chess::expandFEN($blank_FEN);
		$this->assertEquals($blank_expanded, $xFEN);

		$FEN = Chess::packFEN($blank_expanded);
		$this->assertEquals($blank_FEN, $FEN);

		$FENs = [
			'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1' => [
				'rnbqkbnrpppppppp00000000000000000000000000000000PPPPPPPPRNBQKBNR', // xFEN
				'0', // item at pos 33
				[ // board
				  ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R'], // white
				  ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
				  ['0', '0', '0', '0', '0', '0', '0', '0'],
				  ['0', '0', '0', '0', '0', '0', '0', '0'],
				  ['0', '0', '0', '0', '0', '0', '0', '0'],
				  ['0', '0', '0', '0', '0', '0', '0', '0'],
				  ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
				  ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'], // black
				],
			],
			'r1b1k3/pp3p1p/2p5/q3pp2/1b2P1r1/2N3P1/PPP1NPBP/R2QK2R w KQq - 0 13' => [
				'r0b0k000pp000p0p00p00000q000pp000b00P0r000N000P0PPP0NPBPR00QK00R', // xFEN
				'b', // item at pos 33
				[ // board
				  ['R', '0', '0', 'Q', 'K', '0', '0', 'R'], // white
				  ['P', 'P', 'P', '0', 'N', 'P', 'B', 'P'],
				  ['0', '0', 'N', '0', '0', '0', 'P', '0'],
				  ['0', 'b', '0', '0', 'P', '0', 'r', '0'],
				  ['q', '0', '0', '0', 'p', 'p', '0', '0'],
				  ['0', '0', 'p', '0', '0', '0', '0', '0'],
				  ['p', 'p', '0', '0', '0', 'p', '0', 'p'],
				  ['r', '0', 'b', '0', 'k', '0', '0', '0'], // black
				],
			],
			'4r1k1/1b3p1p/ppq3p1/2p5/8/1P3R1Q/PBP3PP/7K' => [
				'0000r0k00b000p0pppq000p000p00000000000000P000R0QPBP000PP0000000K', // xFEN
				'0', // item at pos 33
				[ // board
				  ['0', '0', '0', '0', '0', '0', '0', 'K'], // white
				  ['P', 'B', 'P', '0', '0', '0', 'P', 'P'],
				  ['0', 'P', '0', '0', '0', 'R', '0', 'Q'],
				  ['0', '0', '0', '0', '0', '0', '0', '0'],
				  ['0', '0', 'p', '0', '0', '0', '0', '0'],
				  ['p', 'p', 'q', '0', '0', '0', 'p', '0'],
				  ['0', 'b', '0', '0', '0', 'p', '0', 'p'],
				  ['0', '0', '0', '0', 'r', '0', 'k', '0'], // black
				],
			],
		];

		foreach ($FENs as $FEN => $expected) {
			$xFEN = Chess::expandFEN($FEN);
			$this->assertEquals($expected[0], $xFEN);

			$ref_FEN = $xFEN;
			$item = Chess::FENplace($ref_FEN, 33);
			$this->assertEquals($xFEN, $ref_FEN);
			$this->assertEquals($expected[1], $item);

			$board = Chess::fen_to_board($FEN);
			$board2 = Chess::fen_to_board($expected[0]);
			$this->assertEquals($expected[2], $board);
			$this->assertEquals($expected[2], $board2);

			$fen_board = Chess::board_to_fen($expected[2]);
			$this->assertEquals($expected[0], $fen_board);

			// split the FEN at the first space and only use the first chunk
			$FEN = explode(' ', $FEN);
			$FEN = $FEN[0];

			$back_FEN = Chess::packFEN($xFEN);
			$this->assertEquals($FEN, $back_FEN);
		}

		// test FENplace with item value
		$xFEN     = 'r0b0k000pp000p0p00p00000q000pp000b00P0r000N000P0PPP0NPBPR00QK00R';
		$mod_xFEN = 'r0b0k000pp000p0p00p00x00q000pp000b00P0r000N000P0PPP0NPBPR00QK00R';

		$item = Chess::FENplace($xFEN, 21, 'x');
		$this->assertEquals($mod_xFEN, $xFEN);
		$this->assertEquals('0', $item);
	}


	public function test_id960( ) {
		$id = 518; // standard game

		$pos_blank = Chess::id960_to_pos( );
		$pos_standard = Chess::id960_to_pos($id);

		$this->assertEquals('RNBQKBNR', $pos_blank);
		$this->assertEquals('RNBQKBNR', $pos_standard);

		$ids = [
			 18 => 'BNQNRBKR',
			226 => 'BNRQKBNR',
			493 => 'QRNBKNBR',
			671 => 'RNKRNQBB',
			800 => 'BBRKQNRN',
		];

		// random id
		foreach ($ids as $id => $setup) {
			$pos = Chess::id960_to_pos($id);
			$this->assertEquals($setup, $pos);
		}
	}

}
