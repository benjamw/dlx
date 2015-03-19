<?php

namespace DLX;

class ColumnNode extends Node {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * The number of rows in the column
	 *
	 * @var int
	 */
	protected $count;


	/**
	 * @param string $name optional
	 *
	 * @return ColumnNode
	 */
	public function __construct($name = null) {
		parent::__construct(0, $this);

		$this->count = 0;

		if ( ! empty($name)) {
			$this->name = $name;
		}
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return $this->name;
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
