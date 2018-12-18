<?php
    include('db_connector.php'); 
    session_start();
    
    if(!isset($_SESSION['username'])){
        header('location: login.php');
    }
    if (isset($_GET['logout'])) {
      	session_destroy();
      	unset($_SESSION['username']);
      	header("location: login.php");
    }
    
    
    
    $db = new PDO('mysql:host=127.0.0.1;dbname=ngauge', 'amaterixen', '');
    
    $queryuser = $db->prepare("SELECT * FROM user WHERE username = '".$_SESSION['username']."'");
    $queryuser->execute();
    
    if($queryuser->rowCount() > 0){
        while($fetch = $queryuser->fetch(PDO::FETCH_ASSOC)){
            $uid = $fetch['user_id'];
            //echo $uid;
            //$ucounty = $fetch['user_county'];
        }
    }
    
    $currentuser = $_SESSION['username'];
    //echo $currentuser;
    /*$queryfriends = $db->prepare("SELECT * FROM user WHERE user1 = '".$_SESSION['username']."'");
    $queryfriends->execute();
    
    if($queryfriends->rowCount() > 0){
        while($fetch = $query->fetch(PDO::FETCH_ASSOC)){
            $id = $fetch['user_id'];
            $county = $fetch['user_county'];
        }
    }*/
    
    ?>
<!DOCTYPE html>
<html lang="en">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../HomePage/hpagestyle.css" />
    
    <head>
        <title>N-Gauge</title>
        
        <style>
            .hidden{
                display: none;
            }
        </style>
    </head>
    
    <body>
        <div class="col-xs-5 mx-auto ">
                            <div class="recommendusers">
                                <div class="recommendheader">
                                    Your Friends
                                </div>
                                <div class="recommendbody">
                                    <div class="scrollmenu">
                                        <div class="recommendeduser">
                                            
                                        </div>
                                        
                                        <div class="recommendeduser">
                                            
                                        </div>
                                        
                                        <div class="recommendeduser">
                                            
                                        </div>
    <?php
    require 'Classes/friends.php';
            $queryfriends = $db->prepare("
                SELECT * FROM user WHERE user_id IN(
                SELECT user1 FROM friends WHERE user1 != '".$uid."' AND user2 = '".$uid."' AND friendship_official = '1' OR  user2 != '".$uid."' AND user2 = '".$uid."' AND friendship_official = '1'
                )");
            $queryfriends->execute();
            
            /*$queryfriends = $db->prepare("
                SELECT * FROM user WHERE user_id IN(
                SELECT user1 FROM friends WHERE user2 = '".$uid."' AND friendship_official = '1'
                ) AND user_id IN (
                SELECT user2 FROM friends WHERE user1 = '".$uid."' AND friendship_official = '1'
                )");
            $queryfriends->execute();*/
            
            /*while($fetch = $queryfriends->fetch(PDO::FETCH_ASSOC)){
                $echoboi = $fetch['username'];
                echo $echoboi;
            }*/
            
            while($fetch = $queryfriends->fetch(PDO::FETCH_ASSOC)){
                if($queryfriends->rowCount() > 0){
                //while($fetch = $queryfriends->fetch(PDO::FETCH_ASSOC)){
                $id = $fetch['user_id'];
                $username =  $fetch['username'];
                $fname =  $fetch['Fname'];
                $lname =  $fetch['Lname'];
                $friendcounty = $fetch['user_county'];
                $discordname = "";
                
                //echo $username;
                ?>
                <div>
                    <h4><?php echo $username; ?></h4>
                    <p>Name: <?php echo $fname; ?> <?php echo $lname; ?></p>
                    <p>County: <?php echo $friendcounty; ?></p>
                    <p>Discord: <?php echo $discordname; ?></p>
                    <?php
                    $sharedgamequery = $db->prepare("SELECT G_Name FROM game WHERE G_ID IN (
                        SELECT game_id FROM user_games WHERE user_id = '".$id."' AND game_id IN (
                        SELECT game_id FROM user_games WHERE user_id = '".$uid."'))");
                    $sharedgamequery->execute();
                    ?>
                    
                    <p>You both play: <?php while($fetch = $sharedgamequery->fetch(PDO::FETCH_ASSOC)){
                                $sharedgame = $fetch['G_Name'];
                                echo "'".$sharedgame."' ";
                                
                            }
                        ?>
                    </p>
                    <?php
                    $gamequery = $db->prepare("SELECT G_Name FROM game WHERE G_ID IN (
                        SELECT game_id FROM user_games WHERE user_id = '".$id."')");
                    $gamequery->execute();
                    ?>
                    
                    <p>All games this user plays: <?php while($fetch = $gamequery->fetch(PDO::FETCH_ASSOC)){
                                $playedgame = $fetch['G_Name'];
                                echo "'".$playedgame."' ";
                                
                            }
                        ?>
                    </p>
                    <div class="actions">
                        <?php
                        if($id != $uid){
                            if(friends::renderFriendShip($uid, $id, 'isThereRequestPending') == 1){
                            ?>
                            <button class = "request_pending" disabled>Request Pending</button>
                            <?php
                            }else{
                                if(friends::renderFriendShip($uid, $id, 'isThereFriendShip') == 0){
                                ?>
                                <button class='friendBtn add' data-uid = '<?php echo $id; ?>' data-type='addfriend'>Add as friend</button>
                                <button class = "request_pending hidden" disabled>Request Pending</button>
                                <?php
                                }else{
                                    ?>
                                    <button class='friendBtn unfriend' data-uid='<?php echo $id; ?>' data-type = 'unfriend'>Unfriend</button>
                                <?php 
                                }
                            }
                        }
                }
                else{
                    echo "No friends!";
                }
            }
               
            //}
            
            
        
    //}
            ?>
            </div>
                                </div>
                            </div>
                    </div>
            
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src='js/friends.js'></script>
    </body>
</html>