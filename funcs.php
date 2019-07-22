<?php
require_once('db_connect.php');

function captcha_handler($captcha)
{
    if ($captcha == '') {
        header("Location: index.php?status=1");//Please enter captcha
        exit;
    } else {
        global $db;
        $select = $db->prepare("SELECT `string` FROM `captcha` LIMIT 1;");
        $row = $select->fetch($select->execute());
        $string = $row['string'];
        if ($_POST['captcha'] !== $string) {//Captcha submitted is not the same as the stored one
            header("Location: index.php?status=2");//Captcha entered was wrong
            exit;
        } else {
            return 1;
        }
    }
}

function create_captcha_image($save_as)
{
    global $db;
    $selection = "abcdefghjkmnopqrstuvwxyz0123456789ABCDEFGHJKLMNOPQRSTUVWUXYZ";//Characters to choose from (removed: i, l and I)
    $string = '';
    for ($i = 0; $i < 5; $i++) {//loop 5 times = get 5 characters
        $pos = rand(0, 62);//choose random number between 0 and 62
        $string .= $selection[$pos];// uses the random number to as a pos to select the character ie. 0 = a, 5 = f
    }
    $update = $db->prepare("UPDATE `captcha` SET `string` = :string");//Update the captcha string in the DB
    $update->execute(array(':string' => $string));
    $image = ImageCreate(60, 24) or die ("Error making image");//make the base image
    $background_color = ImageColorAllocate($image, rand(0, 255), rand(0, 255), rand(0, 255));//base image background color
    $text_color = ImageColorAllocate($image, 255, 255, 255);//White text
    $img = ImageString($image, rand(20, 50), rand(1, 8), rand(1, 8), $string, $text_color);//text position
    $img = Imagepng($image, $save_as);//finish the image generation
}

function flip_a_coin()//Returns either 1 or 2
{
    $coin = array('1', '2');
    return array_rand($coin, 1);
}

function flip_for_captcha($coin)
{//If result of 'coin flip' is 1 then generate a new captcha
    if ($coin == '1') {
        create_captcha_image('captcha.png');//create the new captcha and saves the captach image as captcha.png
    }
}

function check_auth($result, $uid)//If 5 wrong attempts lock account for 10 minutes
{
    global $db;
    $statement = $db->prepare("SELECT `locked`, `lock_until`, `attempts` FROM `users` WHERE `uid` = :uid;");
    $statement->execute(array(':uid' => $uid));
    $row = $statement->fetch();
    $db_locked_status = $row['locked'];
    $db_lu = $row['lock_until'];
    $db_attempts = $row['attempts'];
    $time = new DateTime();
    $time->add(new DateInterval('PT10M'));//add 10 minutes onto current date time
    $time_added = $time->format("Y-m-d H:i:s");
    if ($db_locked_status == 0) {//Not locked
        if ($result == 1) {//Set session as not locked
            $statement = $db->prepare("UPDATE `users` SET `attempts` = 0 WHERE `uid` = :uid");
            $statement->execute(array(':uid' => $uid));
            $_SESSION['user'] = $uid;
            header("Location: index.php");
            exit;
        } else {//Password was wrong so:
            if ($db_attempts == 4) {
                $statement = $db->prepare("UPDATE `users` SET `attempts` = 5, `locked` = 1, `lock_until` = :new_time WHERE `uid` = :uid");
                $statement->execute(array(':new_time' => $time_added, ':uid' => $uid));
            } elseif ($db_attempts <= 3) {
                $statement = $db->prepare("UPDATE `users` SET `attempts` = `attempts` + 1 WHERE `uid` = :uid");
                $statement->execute(array(':uid' => $uid));
            }
            header("Location: index.php?status=0");
            exit;
        }
    } elseif ($db_locked_status == 1) {//Locked
        $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $db_lu);//String datetime to datetime object
        if (new DateTime() > $myDateTime) {//Time has passed, set attempts to 0
            $statement = $db->prepare("UPDATE `users` SET `attempts` = 0, `locked` = 0 WHERE `uid` = :uid");//time passed
            $statement->execute(array(':uid' => $uid));
            if ($result == 1) {//Password correct -> set session
                $_SESSION['user'] = $uid;
                header("Location: index.php");
                exit;
            }
        } else {//Time locked until is still ahead (in the future, up coming).
            header("Location: index.php?status=0");
            exit;
        }
    }
}