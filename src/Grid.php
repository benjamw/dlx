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
	protected $path = array(
		'rows' => array( ),
		'cols' => array( ),
	);

	/**
	 * The solutions array
	 *
	 * @var array
	 */
	protected $solutions = array(
		'rows' => array( ),
		'cols' => array( ),
	);

	/**
	 * @var int
	 */
	protected $solutionCount;


	/**
	 * $nodes can be a 2D array
	 * $secondary is the count of secondary columns in the grid.
	 * secondary columns should always be last
	 *
	 * @param array|string $nodes
	 * @param int $columns the grid column count
	 * @param int $secondary the secondary column count
	 *
	 * @throws Exception
	 * @return Grid
	 */
	public function __construct($nodes, $columns, $secondary = 0) {
		$this->h = new ColumnNode(0);
		$this->path = array(
			'rows' => array( ),
			'cols' => array( ),
		);
		$this->solutions = $this->path;
		$this->solutionCount = 0;

		try {
			$this->constructHeaders($columns, $secondary);
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
	 * @param int $secondary optional the secondary column count
	 *
	 * @return void
	 */
	protected function constructHeaders($columns, $secondary = 0) {
		$left = $this->h;

		for ($n = 1; $n <= (int) $columns; ++$n) {
			$new = new ColumnNode($n, (($columns - $n) < $secondary));
			$left = $this->insertRight($new, $left);
		}

		$this->columns = $columns;
	}

	/**
	 * Construct the columns from the given nodes
	 * $nodes can be a 2D array
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

		if ( ! is_array($nodes[0])) {
			$nodes = array_chunk($nodes, $this->columns);

			if ($this->columns !== count(end($nodes))) {
				throw new Exception('Node count is not a multiple of header count');
			}
			reset($nodes);
		}

		$this->rows = count($nodes);

		// as this adds nodes into the grid, the lowest node gets set in
		// the $columns array, and the right-most node gets set in the $rows array
		// so that new nodes can be placed below and to the right of previous nodes.
		// the rows start at 1, because the headers are row 0

		$rows = array( );
		foreach ($nodes as $y => $row) {
			foreach ($row as $x => $value) {
				if ($value) {
					$node = new Node($y + 1, $columns[$x]->getColumn( ));

					// vertical
					$this->insertBelow($node, $columns[$x]);

					// horizontal
					if ( ! empty($rows[$y + 1])) {
						$this->insertRight($node, $rows[$y + 1]);
					}

					$node->getColumn( )->changeCount(1);

					$columns[$x] = $node;
					$rows[$y + 1] = $node;
				}
			}
		}
	}

	/**
	 * Manually select starting rows
	 *
	 * @param array $selectedRows array of row indexes (1-index)
	 *
	 * @throws Exception
	 * @return void
	 */
	public function selectRows($selectedRows) {
		foreach ($selectedRows as $selectedRow) {
			// find the columns this row affects
			$column = $this->findColumn($selectedRow);

			if (0 === $column->getCount( )) {
				// this path has already failed
				throw new Exception('Manually selected rows create an unsolvable problem');
			}

			$this->cover($column);
			for ($row = $column->getDown( ); $row !== $column; $row = $row->getDown( )) {
				if ($selectedRow !== $row->getRow( )) {
					continue;
				}

				$this->addPath('rows', $row->getRow( ));
				$this->addPath('cols', $row->getColumn( )->getCol( ));

				for ($right = $row->getRight( ); $right !== $row; $right = $right->getRight( )) {
					$this->cover($right->getColumn( ));
					$this->addPath('cols', $right->getColumn( )->getCol( ));
				}

				// that's it, don't do anything else
			}
		}
	}

	/**
	 * Manually select starting rows from given cols
	 *   $selectedCols = array(
	 *       array( [columns in a single row] ),
	 *       array( [other columns in a single row] ),
	 *       ...
	 *   );
	 *
	 * @param array $selectedCols array of col indexes (1-index)
	 *
	 * @throws Exception
	 * @return void
	 */
	public function selectCols($selectedCols) {
		if ( ! is_array($selectedCols[0])) {
			$selectedCols = array($selectedCols);
		}

		$rows = array( );

		foreach ($selectedCols as $cols) {
			$colRows = array_combine($cols, array_fill(0, count($cols), array( )));

			// find the rows these columns share
			$right = $this->h->getRight( );
			while ($right !== $this->h) {
				if ( ! in_array($right->getCol( ), $cols)) {
					$right = $right->getRight( );
					continue;
				}

				$col = $right->getCol( );
				$down = $right->getDown( );
				while ($down !== $right) {
					$colRows[$col][] = $down->getRow( );
					$down = $down->getDown( );
				}

				$right = $right->getRight( );
			}

			$intersect = call_user_func_array('array_intersect', $colRows);

			if (1 > count($intersect)) {
				throw new Exception('Given columns do not share a common row');
			}
			elseif (1 < count($intersect)) {
				throw new Exception('Given columns share more than one common row');
			}

			$rows[] = reset($intersect);
		}

		$this->selectRows($rows);
	}

	/**
	 * Search the space for the solutions
	 * If the callback returns a falsy value, the solutions will not be stored locally
	 *
	 * @param int $count solutions to return (0 to return all)
	 * @param callable $callback optional function
	 * @param int $k
	 *
	 * @return bool stop processing
	 */
	public function search($count = 0, $callback = null, $k = 0) {
		if (($this->h->getRight( ) === $this->h) || $this->onlyEmptySecondaryLeft( )) {
			$this->addSolution($callback);

			if ($count && ($count === $this->solutionCount)) {
				return true;
			}

			return false;
		}
		else {
			$column = $this->chooseNextColumn( );
			if (0 === $column->getCount( )) {
				// this path has already failed
				return false;
			}

			$this->cover($column);
			for ($row = $column->getDown( ); $row !== $column; $row = $row->getDown( )) {
				$this->addPath('rows', $row->getRow( ));
				$this->addPath('cols', $row->getColumn( )->getCol( ));

				for ($right = $row->getRight( ); $right !== $row; $right = $right->getRight( )) {
					$this->cover($right->getColumn( ));
					$this->addPath('cols', $right->getColumn( )->getCol( ));
				}

				if ($this->search($count, $callback, $k + 1)) {
					return true;
				}

				$this->removePath( );

				for ($left = $row->getLeft( ); $left !== $row; $left = $left->getLeft( )) {
					$this->uncover($left->getColumn( ));
				}
			}

			$this->uncover($column);
		}

		return false;
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
				// don't use secondary columns
				if ($next->secondary) {
					$next = $next->getRight( );
					continue;
				}

				$nextColumn = $next;
				$lowest = $count;
			}

			$next = $next->getRight( );
		}

		return $nextColumn;
	}

	/**
	 * Find the smallest ColumnNode in the given row
	 *
	 * @param $rowIndex
	 *
	 * @return ColumnNode
	 */
	protected function findColumn($rowIndex) {
		$columns = array( );
		$col = $this->h->getRight( );

		while ($col !== $this->h) {
			$row = $col->getDown( );

			while ($row !== $col) {
				if ($rowIndex === $row->getRow( )) {
					$columns[] = $row->getColumn( );
					$innerCol = $row->getRight( );

					while ($innerCol !== $row) {
						$columns[] = $innerCol->getColumn( );
						$innerCol = $innerCol->getRight( );
					}

					// find the shortest one
					$shortest = reset($columns);
					$lowest = $shortest->getCount( );
					foreach ($columns as $column) {
						if ($column->secondary) {
							continue;
						}

						if ($lowest > $column->getCount( )) {
							$lowest = $column->getCount( );
							$shortest = $column;
						}
					}

					return $shortest;
				}

				$row = $row->getDown( );
			}

			$col = $col->getRight( );
		}

		return $this->h;
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
	 * @param void
	 *
	 * @return bool
	 */
	protected function onlyEmptySecondaryLeft( ) {
		$next = $this->h->getRight( );

		while ($next !== $this->h) {
			if ( ! $next->secondary || $next->getCount( )) {
				return false;
			}

			$next = $next->getRight( );
		}

		return true;
	}

	/**
	 * Add a value to the solution path
	 *
	 * @param string $type the path type ('rows' | 'cols')
	 * @param string $path
	 *
	 * @return void
	 */
	protected function addPath($type, $path) {
		if ('rows' === $type) {
			array_push($this->path[$type], $path);
		}
		elseif ('cols' === $type) {
			$index = count($this->path['rows']) - 1;
			if ( ! array_key_exists($index, $this->path[$type])) {
				$this->path[$type][$index] = array( );
			}

			array_push($this->path[$type][$index], $path);
		}
	}

	/**
	 * Remove the last value from the solution path
	 *
	 * @param void
	 *
	 * @return void
	 */
	protected function removePath( ) {
		array_pop($this->path['rows']);
		array_pop($this->path['cols']);
	}

	/**
	 * Add the complete solution path to the list of solutions
	 * If the callback returns false, the solutions will not be stored locally
	 *
	 * @param callable $callback optional function
	 *
	 * @return void
	 */
	protected function addSolution($callback = null) {
		++$this->solutionCount;

		if (is_callable($callback)) {
			if (false === call_user_func($callback, $this->path)) {
				return;
			}
		}

		array_push($this->solutions['rows'], $this->path['rows']);
		array_push($this->solutions['cols'], $this->path['cols']);
	}

	/**
	 * @param string $type
	 *
	 * @return array
	 */
	public function getSolutions($type = null) {
		if (empty($type)) {
			return $this->solutions;
		}
		else {
			return $this->solutions[$type];
		}
	}

	/**
	 * @param void
	 *
	 * @return int
	 */
	public function getSolutionCount( ) {
		return $this->solutionCount;
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

		// translation array, because $cols may not be contiguous
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
