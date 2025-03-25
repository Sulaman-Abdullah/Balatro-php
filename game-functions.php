<?php
include_once 'card_functions.php';
include_once 'hand_functions.php';


function AnteWon()
{
    $_SESSION["current-hand"] = CreateCurrentHandArray($_SESSION["cards"]);
    $_SESSION["score-needed"] += 300;
    $_SESSION["current-score"] = 0;
    $_SESSION["money"] += $_SESSION["hands"] + $_SESSION["discards"] + 5;
    $_SESSION["hands"] = 200;
    $_SESSION["discards"] = 3;
    $_SESSION["ante"] ++;
    $_SESSION["round"] = 0;
    $_SESSION["already-used-cards"] =
    [
        "clubs" => [],
        "diamonds" => [],
        "hearts" => [],
        "spades" => []
    ];

    if($_SESSION["score-needed"] >= 1000)
    {
        $_SESSION["current-blind"] = "Big blind";
        $_SESSION["blind-img"] = "Big_Blind.png";
        $_SESSION["hands"] = 6;
        $_SESSION["discards"] = 4;
    }
}


function GameOver()
{
    $_SESSION["game-over-screen"] = "";
}
?>