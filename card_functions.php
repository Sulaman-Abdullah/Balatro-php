<?php
include_once 'hand_functions.php';
include_once 'game-functions.php';


// create first instance of random hand and also function for creating new random hand


function GetRandomKey()
{
    $randomnum = rand(0,3);
    switch($randomnum)
    {
        case 0:
            return "clubs";
        case 1:
            return "diamonds";
        case 2:
            return "hearts";
        case 3:
            return "spades";
    }
}

function GetRandomCard($array,$key)
{
    do
    {    
        $randomindex = rand(0,count($array[$key])-1);
        $randomcard = $array[$key][$randomindex]; 
    }
    while(in_array($randomcard,$_SESSION["already-used-cards"][$key])); 
    array_push($_SESSION["already-used-cards"][$key],$randomcard);
    return $randomcard;
}

function CreateCurrentHandArray($array)
{
    $randomCards = [];
    for($i = 0; $i < 8; $i++)
    {
        $key = GetRandomKey();
        $card = GetRandomCard($array,$key);
        $randomCards[$i]= $key."/".$card;
    }
    return $randomCards;  //return this: $randomCards = ["suit/card", "hearts/10", ......]
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function GetChipsAndMultiplyerGained($handtype,$cardsPlayed)
{
    switch($handtype)
    {
        case "Straight Flush":
            $_SESSION["multiplyer"] = 8;
            $_SESSION["chips"] = 100 + array_sum($cardsPlayed);
            break;
        case "Four of a Kind":
            $_SESSION["multiplyer"] = 7;
            $_SESSION["chips"] = 60 + 4*FindValueOfDuplicateCards(array_count_values($cardsPlayed),4,null);
            break;
        case "Full House":
            $_SESSION["multiplyer"] = 4;
            $_SESSION["chips"] = 40 + array_sum($cardsPlayed);
            break;
        case "Flush":
            $_SESSION["multiplyer"] = 4;
            $_SESSION["chips"] = 35 + array_sum($cardsPlayed);
            break;
        case "Straight":
            $_SESSION["multiplyer"] = 4;
            $_SESSION["chips"] = 30 + array_sum($cardsPlayed);
            break;
        case "Three of a Kind":
            $_SESSION["multiplyer"] = 3;
            $_SESSION["chips"] = 30 + 3*FindValueOfDuplicateCards(array_count_values($cardsPlayed),3,null);
            break;
        case "Two Pair":  
            $_SESSION["multiplyer"] = 2;
            $firstPair = FindValueOfDuplicateCards(array_count_values($cardsPlayed),2,null);
            $_SESSION["chips"] = 20 + 2*$firstPair + 2*FindValueOfDuplicateCards(array_count_values($cardsPlayed),2,$firstPair);
            break;
        case "Pair":
            $_SESSION["multiplyer"] = 2;
            $_SESSION["chips"] = 10 + 2*FindValueOfDuplicateCards(array_count_values($cardsPlayed),2,null); 
            break;
        case "High Card":
            $_SESSION["multiplyer"] = 1;
            $_SESSION["chips"] = 5 + max($cardsPlayed);
            break;
    }
}

function FindValueOfDuplicateCards($array,$amount,$valueForTwoPair)
{
    foreach($array as $i)
    {
        $searchedValue = array_search($i,$array);
        if(isset($valueForTwoPair))
        {
            if($i == $amount && ($searchedValue != $valueForTwoPair))
            {
                return $searchedValue;
            }
        }
        elseif($i == $amount)
        {
            return $searchedValue;
        }
    }
}
function ConvertSpecialCards($card)
{
    switch ($card) 
    {
        case "ace.png":
            return 1;
        case "king.png":
            return 11;
        case "queen.png":
            return 12;
        case "joker.png":
            return 13;
    }
}
function CreateArrayForCalcPoints($array,$index)
{
    $returnArray = [];
    foreach($array as $i)
    {
        $temp = explode("/",$i);            //out of "suit/card" makes ["suit","card"] 
        if($index == 1 && ($temp[1] =="ace.png" || $temp[1] == "king.png" || $temp[1] == "queen.png" || $temp[1] == "joker.png"))      // if the card is a special card then converts it into num 
        {                                                                                                    // and adds it into the array
            array_push($returnArray,ConvertSpecialCards($temp[$index])); 
        }
        elseif($index == 1)
        {
            array_push($returnArray,(int)$temp[$index]);
        }
        else                                                                                                  //if u want suits, adds it here
        {array_push($returnArray,$temp[$index]);}
        
    }
    return $returnArray;
}

function EvaluateHand($array)
{
    $suits = CreateArrayForCalcPoints($array,0);
    $ranks = CreateArrayForCalcPoints($array,1);

    $sameRanks = array_count_values($ranks); // find out how many of the same card there are, E.g 5_h, 5_c, 10_s    ["5"=>2, "10"=>1] cuz 2 5s and 1 10
    $uniqueRanks = array_keys($sameRanks);          // find out how many diffrent cards are                                [5,10]

    sort($uniqueRanks);
    rsort($sameRanks);

    $isFlush = count(array_unique($suits)) == 1 && count($uniqueRanks) == 5;
    $isStraight = (count($uniqueRanks) === 5 && max($uniqueRanks) - min($uniqueRanks) === 4); 

    if ($isStraight && $isFlush) {return "Straight Flush";}
    elseif ($sameRanks[0] === 4) {return "Four of a Kind";}
    elseif ($sameRanks[0] === 3 && @$sameRanks[1] === 2) {return "Full House";}
    elseif ($isFlush) {return "Flush";}
    elseif ($isStraight) {return "Straight";}
    elseif ($sameRanks[0] === 3) {return "Three of a Kind";}
    elseif ($sameRanks[0] === 2 && @$sameRanks[1] === 2) {return "Two Pair";}
    elseif ($sameRanks[0] === 2) {return "Pair";}

    return "High Card";
}

function ReplaceSelectedCards($selectedCards)
{
    foreach($selectedCards as $i)
    {
        if(in_array($i, $_SESSION["current-hand"]))
        {
            $newSuit = GetRandomKey(); 
            $newCard = GetRandomCard($_SESSION["cards"],$newSuit);

            $index = array_search($i,$_SESSION["current-hand"]);       //get the index of where $i is in the current hand array
            $_SESSION["current-hand"][$index] = $newSuit . "/". $newCard;
        }
    }
}



?>