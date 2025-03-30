<?php
include_once 'card_functions.php';
include_once 'game-functions.php';

function CheckHand($cards)
{
    if(count($cards) <= 5)
    {
        $ranks = CreateArrayForCalcPoints($cards,1);
        $_SESSION["hand-type"] = EvaluateHand($cards);
        GetChipsAndMultiplyerGained($_SESSION["hand-type"], $ranks);
    }
    elseif(count($cards) > 5 )
    {
        $_SESSION["hand-type"] = "Max 5 cards";
    }
    
}

function PlayHand($cards)
{
    if(count($cards) <= 5 )
    {
        $_SESSION["hands"] -- ;
        $_SESSION["round"] ++ ;
        $_SESSION["total-rounds-played"] ++ ;

        $ranks = CreateArrayForCalcPoints($cards,1);        //get the ranks
        $_SESSION["hand-type"] = EvaluateHand($cards);      //find out what type of hand : "high card" or "Flush" etc ..
        GetChipsAndMultiplyerGained($_SESSION["hand-type"],$ranks);

        $_SESSION["current-score"] += $_SESSION["multiplyer"] * $_SESSION["chips"];   //add the gained score to the current score
        $_SESSION["total-chips-earned"] += $_SESSION["multiplyer"] * $_SESSION["chips"];
        $_SESSION["multiplyer"] = 0;
        $_SESSION["chips"] = 0;

        ReplaceSelectedCards($cards); // replace the selected cards
        $_SESSION["hand-type"] = "Press check hand";
        
        if($_SESSION["hands"] <= 0 && $_SESSION["current-score"] != $_SESSION["score-needed"])
        {
            GameOver();
        }
        if($_SESSION["current-score"] >= $_SESSION["score-needed"])
        {
            AnteWon();
        }
    }
    elseif(count($cards) > 5 )
    {
        $_SESSION["hand-type"] = "Max 5 cards";
    }
}

function Discard($cards)
{
    if(count($cards) <= 5 && $_SESSION["discards"] > 0)
    {
        $_SESSION["discards"] --;
        ReplaceSelectedCards($cards);
        $_SESSION["hand-type"] = "Press check hand";
    }
    elseif($_SESSION["discards"] <= 0)
    {
        $_SESSION["hand-type"] = "No discards left";
    }
    else
    {
        $_SESSION["hand-type"] = "Max 5 cards";
    }

}

?>