<?php

namespace DLX;

class ColumnNode extends Node {

	/**
	 * If true, this column does not need to be covered
	 *
	 * @var bool
	 */
	public bool $secondary;

	/**
	 * @var int
	 */
	public int $col;

	/**
	 * The number of rows in the column
	 *
	 * @var int
	 */
	public int $count;


	/**
	 * @param int  $col
	 * @param bool $secondary
	 */
	public function __construct(int $col, bool $secondary = false) {
		parent::__construct(0, $this);

		$this->col = $col;
		$this->secondary = $secondary;
		$this->count = 0;
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return (string) $this->col;
	}

	/**
	 * @param int $value
	 *
	 * @return void
	 */
	public function changeCount(int $value) {
		$this->count += $value;

		if (0 > $this->count) {
			$this->count = 0;
		}
	}

}
