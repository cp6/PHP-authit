<?php
require_once('db_connect.php');

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