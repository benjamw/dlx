<?php

namespace DLX;

class ColumnNode extends Node {

	/**
	 * @var int
	 */
	protected $col;

	/**
	 * The number of rows in the column
	 *
	 * @var int
	 */
	protected $count;


	/**
	 * @param int $col
	 *
	 * @return ColumnNode
	 */
	public function __construct($col) {
		parent::__construct(0, $this);

		$this->col = (int) $col;
		$this->count = 0;
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return $this->col;
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getCol( ) {
		return $this->col;
	}

	/**
	 * @param int $value
	 *
	 * @return void
	 */
	public function changeCount($value) {
		$this->count += $value;

		if (0 > $this->count) {
			$this->count = 0;
		}
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getCount( ) {
		return $this->count;
	}

}
