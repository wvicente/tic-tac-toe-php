<?php
/*
if($_SERVER['REQUEST_METHOD'] !== "POST"){
    header('Location: /', true, 303);
    die ();
};
*/

$headers = getallheaders();
if (strpos($headers["Content-Type"], "application/json") !== false)
    $_POST = json_decode(file_get_contents("php://input"), true) ?: [];

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
    ["x","","o"],
    ["o","x","o"],
    ["x","",""]
];

$player = "x";
$adversary = "o";

function checkVictory(array $board = null, string $player = null): int
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
    if (is_null($board) || $player == "" ) { throw new Exception("Invalid Player."); }

    if(sizeof(findEmpty($board)) == 9) { return 0; }

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


function minimax(array $board=null, string $player=null, int $deph=null, bool $isMaxPlayer=true): int
{
    if (is_null($board)) { throw new Exception("Invalid Board."); }
    if (is_null($player)) { throw new Exception("Invalid Player."); }
    if (is_null($deph)) { throw new Exception("Invalid Deph."); }

    $score = checkVictory($board, $player);// * ( $isMaxPlayer ? 1 : -1 );

    $empty = findEmpty($board);
    // if ($deph==0 || sizeof($empty)==0)
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
                $board[$k][$v] = $player;
                $bestValue = min($bestValue, minimax($board, $player !== "x"?"x":"o", $deph-1, !$isMaxPlayer));
                print_r("[bestValue: $bestValue ]\r");
            }
        }
        return $bestValue + $deph;
    } else {
        $bestValue = -1000;
        foreach($empty as $list){
            foreach($list as $k=>$v)
            {
                $board[$k][$v] = $player;
                $bestValue = max($bestValue, minimax($board, $player !== "x"?"x":"o", $deph-1, !$isMaxPlayer));
                print_r("[bestValue: $bestValue ]\r");
            }
        }
        return $bestValue - $deph;
    }
}

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
            $_board[$key][$value] = $player;
            $_board_result = minimax($board, $player, $moves);

            print_r("[resultado: $_board_result][$key][$value]\r");

            if ($_board_result > $best_value)
            {
                $best_value = $_board_result;
                $best_board = $_board;
            }
        }
    }

    return $best_board;
}

?>
<pre>
<?

// $score = [
//     'x' => 10,
//     'o' => -10
// ];

// print_r($score);

// print_r(findEmpty($board));
print_r($board);
print_r("[======================================]\r");
print_r(findBestMove($board,$player));
// $board[2][1] = 'x';
// $board[0][1] = 'x';
// print_r(minimax($board, $player, sizeof(findEmpty($board))));
// $board[1][2] = "x";
// print_r(minimax($board, $player, 9));


// var_dump(checkVictory($board, $player));
// var_dump(checkVictory($_POST['board'], $_POST['player']));
// var_dump(array_vertical_slice($board, 3, 3));
// var_dump(array_diagonal_slice($board, 3, 3));
// print_r($_POST);
?>
</pre>