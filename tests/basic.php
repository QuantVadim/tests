<?php  

include "config.php";

$USER = false;

function loginUser(){
	global $USER;
	global $_POST;
	global$_COOKIE;
	global $config;
	global $db;
	$login = $_COOKIE['login'];
	$access_key = $_COOKIE['access_key'];
	$user_id = (int)$_COOKIE['user_id'];
	if($_POST['logout'] != "true"){
		if(strlen($login) > 3 && strlen($access_key) > 3 && strlen($user_id) > 0){
			$q = $db->query("SELECT users.id 'id', users.email 'email', users.login 'login', users.name 'name' from access_keys left join users on access_keys.user_id = users.id where access_keys.user_id = '$user_id' and access_keys.access_key = '$access_key' limit 1");
			if($row = mysqli_fetch_assoc($q)){
				$USER = $row;
				$USER['access_key'] = $access_key;
			}
		}
	if(!$USER){
		$login = $_POST['login'];
		$password = $_POST['password'];
		if(strlen($login) > 3 && strlen($password) > 3){
			$q = $db->query("SELECT * from users where login = '$login' and password = $password limit 1");
			if($row = mysqli_fetch_assoc($q)){
				$user_id = $row['id'];
				$key = rand(10000000000, 99999999999);
				$sq = $db->query("INSERT into access_keys (user_id, access_key) values('$user_id', '$key') ");
				if($sq){
					$USER = $row;
					$USER['access_key'] = $key;
					setcookie("login", $USER['login'], time()+3600*24*30*12, "/");
					setcookie("user_id", $USER['id'], time()+3600*24*30*12, "");
					setcookie("access_key", $USER['access_key'], time()+3600*24*30*12, "/");
				}
			}
		}
	}
	}else{

		if(strlen($access_key) > 3 && strlen($user_id)> 0){
			$q = $db->query("DELETE from access_keys where user_id = '$user_id' and access_key = '$access_key' limit 1");
			if($q){
				setcookie("login", $USER['login'], time()-1, "/");
				setcookie("user_id", $USER['id'], time()-1, "");
				setcookie("access_key", $USER['access_key'], time()-1, "/");
			}
		} 
	}
	$USER = (object)$USER;
}

function getUserbyId($userID){
	global $db;
	$ret = false;
	$userID = (int)$userID;
	$q = $db->query("SELECT * from users where id = $userID");
	if($row = mysqli_fetch_assoc($q)){
		$ret = (object)$row;
	}
	return $ret;
}



?>