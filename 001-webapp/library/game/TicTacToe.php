<?php
namespace Game;

class MinimaxResult {
	public const WIN = 10;
	public const LOST = -10;
	public const DRAW = 0;

	public array $move = [];
	public int $score;
}

class TicTacToeResult {
	public array $board;
	public int $gameStatus;
	public int $available_moves;
}

class TicTacToe {

	// difficulty
	public const EASY = 0; // equals random movement
	public const NORMAL = 4; // not too deep
	public const HARD = 5; // best winning scenario

	public const X = "x";
	public const O = "o";

	private string $huPlayer = "x";
	private string $aiPlayer = "o";
	private int $maxDepth = self::HARD;
	private array $board = [["","",""],["","",""],["","",""]];

	function set_difficulty(int $difficulty): TicTacToe
	{
		$this->maxDepth = $difficulty;
		return $this;
	}

	public function set_Players(string $human): TicTacToe
	{
		switch($human)
		{
			case self::X:
				$this->huPlayer = self::X;
				$this->aiPlayer = self::O;
				break;
			case self::O:
				$this->huPlayer = self::O;
				$this->aiPlayer = self::X;
				break;
			default:
				throw new Exception("Invalid Player.");
		}

		return $this;
	}

	public function set_board(array $board): TicTacToe
	{
		$this->board = $board;
		return $this;
	}

	private function findEmpty(array $board = null): array
	{
		if (is_null($board)) { $board = $this->board; }
		$empty = [];

		for($r=0; $r<sizeof($board); $r+=1)
		{
			for($c=0; $c<sizeof($board[$r]); $c+=1)
			{
				if ($board[$r][$c] == "")
					array_push($empty,[$r => $c]);
			}
		}
		return $empty;
	}

	private function checkVictory(array $board = null, bool $isAiPlayer): int
	{
		if (is_null($board)) { $board = $this->board; }
		if(sizeof($this->findEmpty($board)) === 9) { return 0; }

		$status = 0;
		$player = $isAiPlayer ? $this->aiPlayer : $this->huPlayer;
		$adversary = !$isAiPlayer ? $this->aiPlayer : $this->huPlayer;

		$rows = $cols = sizeof($this->board);

		$_vertical_slice = function(array $board = null, int $rows, int $cols): array
		{
			$new_board = [];
			
			for($r=0; $r<$rows; $r+=1){
				$col = [];
				for($c=0; $c<$cols; $c+=1)
					array_push($col,$board[$c][$r]);
				array_push($new_board, $col);
			}
			return $new_board;
		};

		$_diagonal_slice = function(array $board = null, int $rows, int $cols): array
		{
			if (is_null($board)) { throw new Exception("Invalid Board."); }
			$_board = [];
			$col_1 = [];
			$col_2 = [];
			
			for($r=0; $r<$rows; $r+=1){
				array_push($col_1,$board[$r][$r]);
				array_push($col_2,$board[$r][($cols-1)-$r]);
			}
	
			array_push($_board, $col_1);
			array_push($_board, $col_2);
			return $_board;
		};

		$_victory = function(array $board, string $player, string $adversary, int $rows, int $cols): int
		{
			$status = MinimaxResult::DRAW;
			foreach($board as $line)
			{
				$_line = array_count_values($line);
				
				if(array_key_exists($player,$_line))
					if($_line[$player] == $cols) { $status = MinimaxResult::WIN; break; }

				if(array_key_exists($adversary,$_line))
					if($_line[$adversary] == $cols) { $status = MinimaxResult::LOST; break;}
			}
			return $status;
		};

		# horizontal
		$status = $_victory($board, $player, $adversary, $rows, $cols);
		if ($status !== 0) { return $status; }

		# vertical
		$v_board = $_vertical_slice($board, $rows, $cols);
		$status = $_victory($v_board, $player, $adversary, $rows, $cols);
		if ($status !== 0) { return $status; }

		# diagonal
		$d_board = $_diagonal_slice($board, $rows, $cols);
		$status = $_victory($d_board, $player, $adversary, $rows, $cols);
		if ($status !== 0) { return $status; }
		
		return $status;
	}

	private function minimax(array $board = null, bool $isHuman = true, int $depth = 0): MinimaxResult
	{
		if (is_null($board)) { $board = $this->board; }

		$result = new MinimaxResult();

		$empty = $this->findEmpty($board); // empty spots

		shuffle($empty);

		$score = $this->checkVictory($board, true); // check ai victory

		if ($score === MinimaxResult::WIN){ // if ai wins
			$result->score = MinimaxResult::WIN;
			return $result;
		} else if ($score === MinimaxResult::LOST){ // if ai loose
			$result->score = MinimaxResult::LOST;
			return $result;
		} else if (sizeof($empty) === MinimaxResult::DRAW){ // if do not exist more possible places
			$result->score = MinimaxResult::DRAW;
			return $result;
		}

		if($depth > $this->maxDepth)
		{
			$result->score = MinimaxResult::DRAW;
			return $result;
		}

		$moves = [];

		foreach($empty as $_moves)
		{
			foreach($_moves as $row => $col)
			{
				$move = new MinimaxResult();
				
				// set move as designed player
				$board[$row][$col] = $isHuman ? $this->huPlayer : $this->aiPlayer;

				$move->move = $board;

				if($isHuman)
				{
					$_result = $this->minimax($board, false, $depth+1);
					$move->score = $_result->score;
				} else {
					$_result = $this->minimax($board, true, $depth+1);
					$move->score = $_result->score;
				}

				// reset move spot
				$board[$row][$col] = "";

				// add move to list
				array_push($moves, $move);
			}
		}

		$bestMove = null;
		if(!$isHuman)
		{
			// if aiPlayer get the highest score
			$bestScore = -10000;
			foreach($moves as $move)
			{
				if($move->score > $bestScore)
				{
					$bestScore = $move->score;
					$bestMove = $move;
				}
			}
		} else {
			// if huPlayer get the lowest score
			$bestScore = 10000;
			foreach($moves as $move)
			{
				if($move->score < $bestScore)
				{
					$bestScore = $move->score;
					$bestMove = $move;
				}
			}
		}

		return $bestMove;
	}

	public function Play(): TicTacToeResult
	{
		$result = new TicTacToeResult();
		$game = $this->minimax(null, false);

		$result->board = $game->move;
		$result->gameStatus = $game->score * (-1);
		$result->available_moves = sizeof($this->findEmpty($game->move));

		return $result;
	}
}
?>