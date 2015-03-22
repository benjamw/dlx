<?php

namespace DLX;

use \Exception;

class Grid {

	/**
	 * @var bool
	 */
	public $simple = false;

	/**
	 * The header node
	 *
	 * @var ColumnNode
	 */
	protected $h;

	/**
	 * The column count (not including $h)
	 *
	 * @var int
	 */
	protected $columns;

	/**
	 * The row count (not including headers)
	 *
	 * @var int
	 */
	protected $rows;

	/**
	 * The path to the current state
	 *
	 * @var array
	 */
	protected $path;

	/**
	 * The solutions array
	 *
	 * @var array
	 */
	protected $solutions;


	/**
	 * @param array|string $nodes
	 * @param int $columns the grid column count
	 *
	 * @throws Exception
	 * @return Grid
	 */
	public function __construct($nodes, $columns) {
		$this->h = new ColumnNode(0);
		$this->path = array( );
		$this->solutions = array( );

		try {
			$this->constructHeaders($columns);
			$this->constructNodes($nodes);
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * @param void
	 *
	 * @return string
	 */
	public function __toString( ) {
		return $this->printGrid(false);
	}

	/**
	 * Construct the header row
	 *
	 * @param int $columns the grid column count
	 *
	 * @return void
	 */
	protected function constructHeaders($columns) {
		$left = $this->h;

		for ($n = 1; $n <= (int) $columns; ++$n) {
			$new = new ColumnNode($n);
			$left = $this->insertRight($new, $left);
		}

		$this->columns = $columns;
	}

	/**
	 * Construct the columns from the given nodes
	 *
	 * @param array|string $nodes
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function constructNodes($nodes) {
		if (empty($nodes)) {
			return;
		}

		if (is_string($nodes)) {
			$nodes = str_split($nodes);
		}

		$columns = array( );
		for ($right = $this->h->getRight( ); $right !== $this->h; $right = $right->getRight( )) {
			array_push($columns, $right);
		}

		if (empty($columns)) {
			return;
		}

		if (0 !== (count($nodes) % $this->columns)) {
			throw new Exception('Node count is not a multiple of header count');
		}

		$this->rows = (int) (count($nodes) / $this->columns);

		// the following gets a little weird. basically, what it's doing is
		// traversing the 1-D array grid by counting x across the columns
		// and then when it hits the edge, drops down a y to the next row
		// and resets x and counts across the columns again.
		// but as it adds nodes into the grid, the lowest node gets set in
		// the columns array, and the right-most node gets set in the rows array
		// so that new nodes can be placed below and to the right of those nodes.
		// the rows start at 1, because the headers are row 0

		$x = 0;
		$y = 1;
		$rows = array( );

		foreach ($nodes as $value) {
			if ($value) {
				$node = new Node($y, $columns[$x]->getColumn( ));

				// vertical
				$this->insertBelow($node, $columns[$x]);

				// horizontal
				if ( ! empty($rows[$y])) {
					$this->insertRight($node, $rows[$y]);
				}

				$node->getColumn( )->changeCount(1);

				$columns[$x] = $node;
				$rows[$y] = $node;
			}

			++$x;

			if ($x >= count($columns)) {
				$x = 0;
				++$y;
			}
		}
	}

	/**
	 * Search the space for the solutions
	 *
	 * @param int $k
	 *
	 * @return array solution set
	 */
	public function search($k = 0) {
		if ($this->h->getRight( ) === $this->h) {
			$this->addSolution( );
			return;
		}
		else {
			$column = $this->chooseNextColumn( );
			if (0 === $column->getCount( )) {
				// this path has already failed
				return;
			}

			$this->cover($column);
			for ($row = $column->getDown( ); $row !== $column; $row = $row->getDown( )) {
				$this->addPath($row->getRow( ));

				for ($right = $row->getRight( ); $right !== $row; $right = $right->getRight( )) {
					$this->cover($right->getColumn( ));
				}

				$this->search($k + 1);

				$this->removePath( );

				for ($left = $row->getLeft( ); $left !== $row; $left = $left->getLeft( )) {
					$this->uncover($left->getColumn( ));
				}
			}

			$this->uncover($column);
		}
	}

	/**
	 * Deterministically choose the next column
	 *
	 * @param void
	 *
	 * @return ColumnNode
	 */
	protected function chooseNextColumn( ) {
		if ( ! empty($this->simple)) {
			return $this->h->getRight( );
		}

		$lowest = PHP_INT_MAX; // largest int available
		$next = $nextColumn = $this->h->getRight( );

		while ($next !== $this->h) {
			if (($count = $next->getCount( )) < $lowest) {
				$nextColumn = $next;
				$lowest = $count;
			}

			$next = $next->getRight( );
		}

		return $nextColumn;
	}

	/**
	 * Cover the given column
	 * @see self::uncover
	 *
	 * @param ColumnNode $column
	 *
	 * @return void
	 */
	protected function cover(ColumnNode $column) {
		$column->getRight( )->setLeft($column->getLeft( ));
		$column->getLeft( )->setRight($column->getRight( ));

		// down then right is important here because it is the opposite of the uncover order
		for ($row = $column->getDown( ); $row !== $column; $row = $row->getDown( )) {
			for ($right = $row->getRight( ); $right !== $row; $right = $right->getRight( )) {
				$right->getUp( )->setDown($right->getDown( ));
				$right->getDown( )->setUp($right->getUp( ));
				$right->getColumn( )->changeCount(-1);
			}
		}
	}

	/**
	 * Uncover the given column
	 * @see self::cover
	 *
	 * @param ColumnNode $column
	 *
	 * @return void
	 */
	protected function uncover(ColumnNode $column) {
		// up then left is important here because it is the opposite of the cover order
		for ($row = $column->getUp( ); $row !== $column; $row = $row->getUp( )) {
			for ($left = $row->getLeft( ); $left !== $row; $left = $left->getLeft( )) {
				$left->getUp( )->setDown($left);
				$left->getDown( )->setUp($left);
				$left->getColumn( )->changeCount(1);
			}
		}

		$column->getRight( )->setLeft($column);
		$column->getLeft( )->setRight($column);
	}

	/**
	 * Add a value to the solution path
	 *
	 * @param string $path
	 *
	 * @return void
	 */
	protected function addPath($path) {
		array_push($this->path, $path);
	}

	/**
	 * Remove the last value from the solution path
	 *
	 * @param void
	 *
	 * @return void
	 */
	protected function removePath( ) {
		array_pop($this->path);
	}

	/**
	 * Add the complete solution path to the list of solutions
	 *
	 * @param void
	 *
	 * @return void
	 */
	protected function addSolution( ) {
		array_push($this->solutions, $this->path);
	}

	/**
	 * @param void
	 *
	 * @return array
	 */
	public function getSolutions( ) {
		return $this->solutions;
	}

	/**
	 * Insert a new node above the $down node
	 *
	 * @param Node $new
	 * @param Node $down
	 *
	 * @return Node inserted
	 */
	public function insertAbove(Node $new, Node $down) {
		$new->setUp($down->getUp( ));
		$new->setDown($down);
		$down->getUp( )->setDown($new);
		$down->setUp($new);

		 return $new;
	}

	/**
	 * Insert a new node to the right of the $left node
	 *
	 * @param Node $new
	 * @param Node $left
	 *
	 * @return Node inserted
	 */
	public function insertRight(Node $new, Node $left) {
		$new->setRight($left->getRight( ));
		$new->setLeft($left);
		$left->getRight( )->setLeft($new);
		$left->setRight($new);

		return $new;
	}

	/**
	 * Insert a new node below the $up node
	 *
	 * @param Node $new
	 * @param Node $up
	 *
	 * @return Node inserted
	 */
	public function insertBelow(Node $new, Node $up) {
		$new->setDown($up->getDown( ));
		$new->setUp($up);
		$up->getDown( )->setUp($new);
		$up->setDown($new);

		return $new;
	}

	/**
	 * Insert a new node to the left of the $right node
	 *
	 * @param Node $new
	 * @param Node $right
	 *
	 * @return Node inserted
	 */
	public function insertLeft(Node $new, Node $right) {
		$new->setLeft($right->getLeft( ));
		$new->setRight($right);
		$right->getLeft( )->setRight($new);
		$right->setLeft($new);

		return $new;
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getColumnCount( ) {
		return $this->columns;
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getRowCount( ) {
		return $this->rows;
	}

	/**
	 * Print the grid in a human-readable format
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function printGrid($echo = true) {
		$grid = array( );

		// parse through the headers
		$cols = array( );
		for ($right = $this->h->getRight( ); $right !== $this->h; $right = $right->getRight( )) {
			$cols[] = $right->getCol( );
		}

		// translation array
		$trans = array_flip($cols);

		$next = 1;
		// this loop will not complete in the normal 'for' loop fashion until the process completes.
		// there is a built-in reset inside the loop below because basically, what it's doing is
		// starting on the first column and checking if it has a node in the $next row, and if not,
		// proceeding to the next column. if it does find a node in the $next row, then it fills that
		// row, and then starts the search loop over from the first column again.
		while ($this->rows >= $next) {
			for ($right = $this->h->getRight( ); $right !== $this->h; $right = $right->getRight( )) {
				$down = $right->getDown( );

				// keep going down until the $next row
				// but stop if it loops back to zero
				while ((0 < $down->getRow( )) && ($down->getRow( ) < $next)) {
					$down = $down->getDown( );
				}

				// fill the next row if a match is found
				if ($down->getRow( ) === $next) {
					$row = array_fill(0, count($cols), 0);

					$row[$trans[$down->getColumn( )->getCol( )]] = 1;

					for ($sub_right = $down->getRight( ); $sub_right !== $down; $sub_right = $sub_right->getRight( )) {
						$row[$trans[$sub_right->getColumn( )->getCol( )]] = 1;
					}

					$grid[] = $row;
					++$next;

					// restart the outer loop
					$right = $this->h;
				}
			}

			++$next;
		}

		// build the table
		$html = "<table><thead><tr>";

		foreach ($cols as $col) {
			$html .= "<th>{$col}</th>";
		}

		$html .= "</tr></thead><tbody>";

		foreach ($grid as $row) {
			$html .= "<tr>";

			foreach ($row as $value) {
				$html .= "<td>{$value}</td>";
			}

			$html .= "</tr>";
		}

		$html .= "</tbody></table>";

		if ($echo) {
			echo $html;
		}

		return $html;
	}

}
