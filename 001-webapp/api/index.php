<?php
/*
if($_SERVER['REQUEST_METHOD'] !== "POST"){
	header('Location: /', true, 303);
	die ();
};
*/
require __DIR__ . '/../library/vendor/autoload.php';

$headers = getallheaders();
if (strpos($headers["Content-Type"], "application/json") !== false)
	$_POST = json_decode(file_get_contents("php://input"), true) ?: [];

header("Content-Type: application/json; charset=UTF-8");

use Game\TicTacToe;

$ttt = new TicTacToe();

$result = $ttt
	->set_difficulty(TicTacToe::EASY)
	->set_Players(TicTacToe::X === strtolower($_POST['player']) ? TicTacToe::X : TicTacToe::O)
	->set_board($_POST['board'])
	->Play();

print_r(json_encode($result));

?>