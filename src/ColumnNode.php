<?php

namespace DLX;

class ColumnNode extends Node {

	/**
	 * If true, this column does not need to be covered
	 *
	 * @var bool
	 */
	public $secondary;

	/**
	 * @var int
	 */
	public $col;

	/**
	 * The number of rows in the column
	 *
	 * @var int
	 */
	public $count;


	/**
	 * @param int  $col
	 * @param bool $secondary
	 */
	public function __construct($col, $secondary = false) {
		parent::__construct(0, $this);

		$this->col = (int) $col;
		$this->secondary = (bool) $secondary;
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

}
