<?php
session_start();
if(!isset($_SESSION["cards"]))
{
    $_SESSION["cards"] =[
                "clubs" => ["ace.png","2.png","3.png","4.png","5.png","6.png","7.png","8.png","9.png","10.png","king.png","queen.png","joker.png"], 
                "diamonds" => ["ace.png","2.png","3.png","4.png","5.png","6.png","7.png","8.png","9.png","10.png","king.png","queen.png","joker.png"],
                "hearts" => ["ace.png","2.png","3.png","4.png","5.png","6.png","7.png","8.png","9.png","10.png","king.png","queen.png","joker.png"],
                "spades" => ["ace.png","2.png","3.png","4.png","5.png","6.png","7.png","8.png","9.png","10.png","king.png","queen.png","joker.png"]
            ];
    $_SESSION["already-used-cards"] =
    [
        "clubs" => [],
        "diamonds" => [],
        "hearts" => [],
        "spades" => []
    ];
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
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
        if(isset($valueForTwoPair))
        {
            if($i == $amount && (array_search($i,$array) != $valueForTwoPair))
            {
                return array_search($i,$array);
            }
        }
        elseif($i == $amount)
        {
            return array_search($i,$array);
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
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//Play hand functions and Calculate points 
function GameOver()
{
    $_SESSION["game-over-screen"] = "";
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

function PlayHand($cards)
{
    if(count($cards) <= 5 )
    {
        if($_SESSION["hands"] <= 0)
        {
            GameOver();
        }
        else
        {
            $_SESSION["hands"] -- ;
            $_SESSION["round"] ++ ;
            $_SESSION["total-rounds-played"] ++ ;
            $ranks = CreateArrayForCalcPoints($cards,1);        //get the ranks
            $_SESSION["hand-type"] = EvaluateHand($cards);      //find out what type of hand : "high card" or "Flush" etc ..
            GetChipsAndMultiplyerGained($_SESSION["hand-type"],$ranks);
            $_SESSION["current-score"] += $_SESSION["multiplyer"] * $_SESSION["chips"];   //add the gained score to the current score
            $_SESSION["total-chips-earned"] += $_SESSION["multiplyer"] * $_SESSION["chips"];
            ReplaceSelectedCards($cards); // replace the selected cards
            $_SESSION["hand-type"] = "Press check hand";
        }
    }
    elseif(count($cards) > 5 )
    {
        $_SESSION["hand-type"] = "Max 5 cards";
    }
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//Main 
if(!isset($_SESSION["game-started"]))
{
    $_SESSION["game-started"] = true;
    $_SESSION["current-blind"] = "Small blind";
    $_SESSION["blind-img"] = "Small_Blind.png";
    $_SESSION["score-needed"] = 300;
    $_SESSION["current-score"] = 0;
    $_SESSION["hand-type"] = "Press check hand";
    $_SESSION["multiplyer"] = 0;
    $_SESSION["chips"] = 0;
    $_SESSION["hands"] = 5;
    $_SESSION["discards"] = 3;
    $_SESSION["money"] = 4;
    $_SESSION["ante"] = 1;
    $_SESSION["round"] = 1;
    $_SESSION["total-chips-earned"] = 0;
    $_SESSION["total-rounds-played"] = 0;
    $_SESSION["game-over-screen"] = "display:none;";
    $_SESSION["current-hand"] = CreateCurrentHandArray($_SESSION["cards"]);
    header("Location:Game.php");
}


if(isset($_POST['check']) && isset($_POST["card"]))
{
    $checkedCards = @$_POST["card"];
    CheckHand(@$_POST["card"]);
}
elseif(isset($_POST["play"]) && isset($_POST["card"]) && $_SESSION["game-over-screen"] == "display:none;")
{
    PlayHand($_POST["card"]);
}
elseif(!isset($_POST["cards"]) && (isset($_POST["play"]) || isset($_POST['check']) ))
{
    $_SESSION["hand-type"] = "Atleast 1 card";
}




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balatro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container">
        <div class="game-info" style="<?php if($_SESSION['current-blind'] == 'Small blind') {echo "border-right:#13515A;border-style: none solid none none;";}?>">
            <div class="blind-info">
                <div class="blind-name" style="<?php if($_SESSION['current-blind'] == 'Small blind') {echo "background-color:#025FA3;";}?>">
                    <h1><?php echo $_SESSION["current-blind"];?></h1>
                </div>
                <div class="blind-image" style="<?php if($_SESSION['current-blind'] == 'Small blind') {echo "background-color:#073B53;";}?>">
                    <img src="assets/<?php echo $_SESSION['blind-img'];?>"  style="height: 96px; width: 96px;">
                    <div class="score-needed">
                        <div>Score at least</div>
                        <div style="font-size: 28px; color: red;"><?php echo $_SESSION["score-needed"]?></div>
                        <div>to earn $$$$</div>
                    </div>
                </div>
            </div>
            <div class="score-panel">
                <h1>Round<br>score</h1>
                <div class="current-score"><h1><?php echo $_SESSION['current-score'];?> Chips</h1></div>
            </div>
            <div class="hand-score-details">
                <div style="font-size: 20px;"><h1><?php echo @$_SESSION['hand-type'];?></h1></div>
                <div class="multiplyer-and-chip-box" style="font-size: 24px;">
                    <div class="multiplyer"><h1><?php echo $_SESSION['chips'];?></h1></div>
                    <h1>X</h1>
                    <div class="chips-gain"><h1><?php echo $_SESSION['multiplyer'];?></h1></div>
                </div>
            </div>
            <div class="hands-left">
                <h1 style="font-size: 20px;margin-top: 5px;">Hands</h1>
                <div class="hands-left-num"><?php echo $_SESSION['hands'];?></div>
            </div>
            <div class="discards">
                <h1 style="font-size: 20px;margin-top: 5px;">Discards</h1>
                <div class="discards-left-num"><?php echo $_SESSION['discards'];?></div>
            </div>
            <div class="money">
                <div class="money-num">$<?php echo $_SESSION['money'];?></div>
            </div>
            <div class="ante">
            <h1 style="font-size: 20px;margin-top: 5px;">Ante</h1>
                <div class="ante-num"><?php echo $_SESSION['ante'];?>/3</div>
            </div>
            <div class="round">
            <h1 style="font-size: 20px;margin-top: 5px;">Round</h1>
                <div class="round-num"><?php echo $_SESSION['round'];?></div>
            </div>
            <div class="run-info">
                <form action="" method="post">
                    <input type="submit" name="run-info" value="Run Info">
                </form>
            </div>
            <div class="options"><h1>Options</h1></div>
        </div>
        <form class="player-hand-form" action="" method="post">
            <div id="player-hand" class="hand">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][0];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][0];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][0],$checkedCards)) echo 'Checked';?> class="card0">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][1];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][1];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][1],$checkedCards)) echo 'Checked';?> class="card1">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][2];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][2];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][2],$checkedCards)) echo 'Checked';?> class="card2">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][3];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][3];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][3],$checkedCards)) echo 'Checked';?> class="card3">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][4];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][4];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][4],$checkedCards)) echo 'Checked';?> class="card4">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][5];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][5];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][5],$checkedCards)) echo 'Checked';?> class="card5">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][6];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][6];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][6],$checkedCards)) echo 'Checked';?> class="card6">
                <div class="card"><img src="assets/cards/<?php echo $_SESSION["current-hand"][7];?>"></div><input type="checkbox" name="card[]" value="<?php echo $_SESSION["current-hand"][7];?>" <?php if(isset($checkedCards) && in_array($_SESSION["current-hand"][7],$checkedCards)) echo 'Checked';?> class="card7">
            </div>
            <div class="play-hand"><input type="submit" name="play" value="Play hand"></div>
            <div class="check-hand"><input type="submit" name="check" value="Check hand"></div>
        </form>
        <div class="game-over-container" style="<?php echo $_SESSION["game-over-screen"];?>">
            <h1>Game Over</h1>
            <p>🃏 You ran out of Hands to play! 🃏</p>
            <p><strong>Total chips earned:</strong> <?php echo $_SESSION["total-chips-earned"]?></p>
            <p><strong>Higest Ante:</strong> <?php echo $_SESSION["ante"]?></p>
            <p><strong>Total Rounds Played:</strong> <?php echo $_SESSION["total-rounds-played"]?></p>
            <div class="button-container">
                <a href="#" class="button">Play Again</a>
                <a href="#" class="button exit">Exit to Main Menu</a>
        </div>
    </div>
    </div>
</body>
</html>
