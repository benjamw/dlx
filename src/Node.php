<?php

namespace DLX;

class Node {

	/**
	 * @var int
	 */
	protected $row;

	/**
	 * @var Node
	 */
	protected $up;

	/**
	 * @var Node
	 */
	protected $right;

	/**
	 * @var Node
	 */
	protected $down;

	/**
	 * @var Node
	 */
	protected $left;

	/**
	 * @var ColumnNode
	 */
	protected $column;


	/**
	 * @param int $row
	 * @param ColumnNode $column optional
	 *
	 * @return Node
	 */
	public function __construct($row, ColumnNode $column = null) {
		$this->row = (int) $row;

		$this->up = $this;
		$this->right = $this;
		$this->down = $this;
		$this->left = $this;

		if ($column) {
			$this->column = $column;
		}
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return $this->column.':'.$this->row;
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getRow( ) {
		return $this->row;
	}

	/**
	 * @param void
	 *
	 * @return Node
	 */
	public function getUp( ) {
		return $this->up;
	}

	/**
	 * @param Node $up
	 */
	public function setUp(Node $up) {
		$this->up = $up;
	}

	/**
	 * @param void
	 *
	 * @return Node
	 */
	public function getRight( ) {
		return $this->right;
	}

	/**
	 * @param Node $right
	 *
	 * @return void
	 */
	public function setRight(Node $right) {
		$this->right = $right;
	}

	/**
	 * @param void
	 *
	 * @return Node
	 */
	public function getDown( ) {
		return $this->down;
	}

	/**
	 * @param Node $down
	 *
	 * @return void
	 */
	public function setDown(Node $down) {
		$this->down = $down;
	}

	/**
	 * @param void
	 *
	 * @return Node
	 */
	public function getLeft( ) {
		return $this->left;
	}

	/**
	 * @param Node $left
	 *
	 * @return void
	 */
	public function setLeft(Node $left) {
		$this->left = $left;
	}

	/**
	 * @param void
	 *
	 * @return ColumnNode
	 */
	public function getColumn( ) {
		return $this->column;
	}

	/**
	 * @param ColumnNode $column
	 *
	 * @return void
	 */
	public function setColumn($column) {
		if ($this->column) {
			return;
		}

		$this->column = $column;
	}

}
