<?php
include_once 'card_functions.php';
include_once 'hand_functions.php';


function AnteWon()
{
    $_SESSION["already-used-cards"] =
    [
        "clubs" => [],
        "diamonds" => [],
        "hearts" => [],
        "spades" => []
    ];
    $_SESSION["current-hand"] = CreateCurrentHandArray($_SESSION["cards"]);
    $_SESSION["score-needed"] += 300;
    $_SESSION["current-score"] = 0;
    $_SESSION["money"] += $_SESSION["hands"] + $_SESSION["discards"] + 5;
    $_SESSION["hands"] = 5 + $_SESSION["ante"];
    $_SESSION["discards"] = 3 + $_SESSION["ante"];
    $_SESSION["ante"] ++;
    $_SESSION["round"] = 0;

    if($_SESSION["score-needed"] >= 1000)
    {
        $_SESSION["blind-img"] = "Big_Blind.png";
        header("Location:Game2.php");
    }
}


function GameOver()
{
    $_SESSION["game-over-screen"] = "";
}

function PlayAgian()
{
    session_destroy();
    header("location:Game.php");
}


?>


