<?php
/**
 * Vitoria 1
 * Derrota -1
 * Empate 0
 * 
 *     ROTINA minimax(nó, profundidade, maximizador)
 *         SE nó é um nó terminal OU profundidade = 0 ENTÃO
 *             RETORNE o valor da heurística do nó
 *         SENÃO SE maximizador é FALSE ENTÃO
 *             α ← +∞
 *             PARA CADA filho DE nó
 *                 α ← min(α, minimax(filho, profundidade-1,true))
 *             FIM PARA
 *             RETORNE α
 *         SENÃO
 *             //Maximizador
 *             α ← -∞
 *             //Escolher a maior dentre as perdas causadas pelo minimizador
 *             PARA CADA filho DE nó
 *                 α ← max(α, minimax(filho, profundidade-1,false))
 *             FIM PARA
 *             RETORNE α
 *         FIM SE
 *     FIM ROTINA
 * 
*/

$board = [
    ["","",""],
    ["","",""],
    ["","",""]
];

$player = "x";
$adversary = "o";

function findEmpty(array $board=null): array
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
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

function checkVictory(array $board = null, string $player = null): int
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
    if (is_null($board) || $player === "" ) { throw new Exception("Invalid Player."); }

    if(sizeof(findEmpty($board)) === 9) { return 0; }

    $status = 0;
    $adversary = $player !== "x"?"x":"o";
    $rows = $cols = sizeof($board);

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
        $status = 0;
        foreach($board as $line)
        {
            $_line = array_count_values($line);
            
            if(array_key_exists($player,$_line))
                if($_line[$player] == $cols) { $status = 10; break; }

            if(array_key_exists($adversary,$_line))
                if($_line[$adversary] == $cols) { $status = -10; break;}
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
    
    return 1;
}

/* minmax: for loop
function minmax(array $board=null, string $player=null, int $moves=null, bool $isMax=null): int
{
    $score = checkVictory($board, $player);

    if ($score !== 1) { return $score; }

    if($isMax)
    {
        $best = -1000;
        for($r=0; $r<sizeof($board); $r+=1)
        {
            for($c=0; $c<sizeof($board[$r]); $c+=1)
            {
                if($board[$r][$c] === "")
                {
                    $board[$r][$c] = "x";
                    $best = max($best, minmax($board, "x", sizeof(findEmpty($board)) + 1, false));
                    $board[$r][$c] = "";
                    print_r("[best: $best][$r][$c]\r");
                }
            }
        }
        return $best + $moves;
    } else {
        $best = 1000;
        for($r=0; $r<sizeof($board); $r+=1)
        {
            for($c=0; $c<sizeof($board[$r]); $c+=1)
            {
                if($board[$r][$c] === "")
                {
                    $board[$r][$c] = "o";
                    $best = min($best, minmax($board, "o", sizeof(findEmpty($board)) + 1, true));
                    $board[$r][$c] = "";
                }
            }
        }
        return $best + $moves;
    }
}
*/

// /* [working] minmax: foreach loop
function minmax(array $board=null, string $player=null, int $deph=null, bool $isMaxPlayer=true): int
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
    if (is_null($player)) { throw new Exception("Invalid Player."); }
    if (is_null($deph)) { throw new Exception("Invalid Deph."); }

    $empty = findEmpty($board);
    if(sizeof($empty) === 0)
        return 0;

    $score = checkVictory($board, "x");

    if ($score !== 1)
    {
        return $score;
    }
    else if (!$isMaxPlayer)
    {
        $bestValue = 1000;
        foreach($empty as $list){
            foreach($list as $k=>$v)
            {
                $board[$k][$v] = "x";
                $bestValue = min($bestValue, minmax($board, "x", $deph+1, !$isMaxPlayer));
                $board[$k][$v] = "";
            }
        }
        return $bestValue + $deph;
    } else {
        $bestValue = -1000;
        foreach($empty as $list){
            foreach($list as $k=>$v)
            {
                $board[$k][$v] = "o";
                $bestValue = max($bestValue, minmax($board, "o", $deph+1, !$isMaxPlayer));
                $board[$k][$v] = "";
            }
        }
        return $bestValue - $deph;
    }
}
// */

function findBestMove(array $board, string $player): array
{
    $best_board = [];
    $best_value = -1000;
    $empty = findEmpty($board);
    $moves = sizeof($empty);

    foreach($empty as $list){
        foreach($list as $key=>$value)
        {
            $_board = $board;
            $_board[$key][$value] = "x";
            $_board_result = minmax($_board, $moves, false);

            // print_r("\r[resultado: $_board_result][$key][$value]\r");

            if ($_board_result > $best_value)
            {
                $best_value = $_board_result;
                $best_board = $_board;
            }
        }
    }
    return $best_board;
}

/* working
function minmax(array $board, int $deph, bool $isMax = true): int
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
    if (is_null($deph)) { throw new Exception("Invalid Deph."); }

    $score = checkVictory($board, "x");

    if($score === 10)
        return $score;

    if($score === -10)
        return $score;

    if(sizeof(findEmpty($board)) === 0)
        return 0;

    if($isMax)
    {
        $best = -1000;
        foreach($board as $r => $r_val)
        {
            foreach($r_val as $c => $c_val)
            {
                if($board[$r][$c] === "")
                {
                    $board[$r][$c] = "x";
                    $best = max( $best, minmax($board, $deph+1, !$isMax));
                    $board[$r][$c] = "";
                }
            }
        }

        return $best-$deph;
    } else {
        $best = 1000;
        foreach($board as $r => $r_val)
        {
            foreach($r_val as $c => $c_val)
            {
                if($board[$r][$c] === "")
                {
                    $board[$r][$c] = "o";
                    $best = min( $best, minmax($board, $deph+1, !$isMax));
                    $board[$r][$c] = "";
                }
            }
        }
        return $best+$deph;
    }
}
*/

?>
<pre>
<?php
// print_r(findEmpty($board));
// print_r(checkVictory($board, $player));

// print_r(minmax($board, $player, sizeof(findEmpty($board)), false));

// $start = microtime(true);
// print_r(findBestMove($board,"x"));
// print(microtime(true) - $start);

/*
$board = [
    ["a","b","c"],
    ["d","e","f"],
    ["g","h","i"]
];

print_r("[======= for loop =======]\r");

for($r=0; $r<sizeof($board); $r+=1)
{
    print_r("[row: $r]");
    for($c=0; $c<sizeof($board[$r]); $c+=1)
    {
        print_r("[col: $c]");
    }
    print_r("\r");
}

print_r("\r[===== foreach loop =====]\r");

foreach($board as $r => $r_val)
{
    print_r("[row: $r]");
    foreach($r_val as $col => $c_val)
    {
        print_r("[col: $col]");
    }
    print_r("\r");
}
*/
?>
</pre>