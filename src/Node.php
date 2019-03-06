<?php

namespace DLX;

class Node {

	/**
	 * @var int
	 */
	public $row;

	/**
	 * @var ColumnNode
	 */
	public $column;

	/**
	 * @var Node
	 */
	public $up;

	/**
	 * @var Node
	 */
	public $right;

	/**
	 * @var Node
	 */
	public $down;

	/**
	 * @var Node
	 */
	public $left;


	/**
	 * @param int        $row
	 * @param ColumnNode $column optional
	 */
	public function __construct($row, ColumnNode $column = null) {
		$this->row = (int) $row;

		if ($column) {
			$this->column = $column;
		}

		$this->up = $this;
		$this->right = $this;
		$this->down = $this;
		$this->left = $this;
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return $this->column.':'.$this->row;
	}

}
