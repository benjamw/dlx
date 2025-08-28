# DLX - Dancing Links Puzzle Solvers

A collection of puzzle solvers using the Dancing Links (DLX) algorithm, including polyomino, polyhex, and isometric puzzle solvers.

## Features

- **Polyominoes**: Classic 2D polyomino puzzles with 90-degree rotations
- **Polyhexes**: Isometric polyomino puzzles with 60-degree rotations
- **Multiple Puzzle Types**: Pentominoes, Sudoku, Queens, Soma Cube, and more
- **Flexible Layouts**: Support for custom board layouts and piece configurations
- **Efficient Solving**: Uses the Dancing Links algorithm for optimal performance

## Installation

### Via Composer (Recommended)

```bash
composer require benjamw/dlx
```

### Manual Installation

1. Clone this repository
2. Run `composer install`
3. Include the autoloader in your project

```php
require_once 'vendor/autoload.php';
```

## Usage

### Basic Polyomino Puzzle

```php
use DLX\Puzzles\Pentominoes;

// Create a 6x10 board
$puzzle = new Pentominoes(10, 6);

// Solve the puzzle
$solutions = $puzzle->solve();

// Get the first solution
$firstSolution = $solutions[0];
```

### Isometric Polyhex Puzzle

```php
use DLX\Puzzles\Polyhexes;

// Create a custom isometric layout
$layout = [
    [1, 1, 1, 0],
    [1, 1, 1, 1],
    [0, 1, 1, 1],
];

$puzzle = new Polyhexes($layout);

// Solve with 60-degree rotations
$solutions = $puzzle->solve();
```

### Custom Layout

```php
// Define a custom board layout using string format
$layout = "
    . . . . . . . .
    . * * * * * * .
    . * * * * * * .
    . * * * * * * .
    . . . . . . . .
";

$puzzle = new Pentominoes($layout);
```

### Placing Specific Pieces

```php
// Place specific pieces in certain positions
$puzzle->place(['A', 'B'], ['C']);

// Exclude certain piece positions
$puzzle->exclude(['A'], ['B']);
```

## Available Puzzle Types

- **Pentominoes**: 12 different 5-square pieces
- **Tetrominoes**: 7 different 4-square pieces
- **Polyhexes**: Isometric hexagonal pieces
- **Sudoku**: Classic 9x9 grid puzzle
- **Queens**: N-Queens problem
- **Soma Cube**: 3D cube assembly puzzle
- **Kanoodle**: Isometric polyhex puzzles

## Testing

Run the test suite:

```bash
# Run all tests
composer test

# Run with coverage report
composer test:coverage
```

## Architecture

The project uses a hierarchical class structure:

- **`Grid`**: Core Dancing Links implementation
- **`Polyominoes`**: Base class for 2D polyomino puzzles
- **`Polyhexes`**: Extended class for isometric puzzles with 60-degree rotations
- **Specific Puzzle Classes**: Extend the base classes for specific puzzle types

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Examples

See the `puzzles/` directory for specific puzzle implementations and the `tests/` directory for usage examples.

## Requirements

- PHP 7.4 or higher
- Composer for dependency management
