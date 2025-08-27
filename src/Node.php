<?php

namespace DLX;

class Node {

	/**
	 * @var int
	 */
	public int $row;

	/**
	 * @var ColumnNode
	 */
	public ColumnNode $column;

	/**
	 * @var Node
	 */
	public Node $up;

	/**
	 * @var Node
	 */
	public Node $right;

	/**
	 * @var Node
	 */
	public Node $down;

	/**
	 * @var Node
	 */
	public Node $left;


	/**
	 * @param int        $row
	 * @param ?ColumnNode $column optional
	 */
	public function __construct(int $row, ColumnNode $column = null) {
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
