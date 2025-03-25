<?php
session_start();
include_once 'card_functions.php';
include_once 'hand_functions.php';
include_once 'game-functions.php';



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
    $_SESSION["hands"] = 200;
    $_SESSION["discards"] = 3;
    $_SESSION["money"] = 5;
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
                <div class="ante-num"><?php echo $_SESSION['ante'];?>/6</div>
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
