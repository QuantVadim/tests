<?php  

include "config.php";
$dddd = "delete me!";

$USER = false;

function intarr($arr){
	$ret = [];
	$keys = array_keys($arr);
	for ($i=0; $i < count($arr); $i++) { 
		if($arr[$keys[$i]] > 0 ) $ret[] = (int)$arr[$keys[$i]];
	}
	return $ret;
}

function arr_diff($arr1, $arr2)
{
	$ret = [];
	for ($i=0; $i < count($arr1); $i++) { 
		$isAdd = true;
		for ($j=0; $j < count($arr2); $j++) { 
			if($arr1[$i] == $arr2[$j]) $isAdd = false;
		}
		if($isAdd) $ret[$i] = (int)$arr1[$i];
	}
	return $ret;
}

function unite($arr1, $arr2){
	$merg = array_merge($arr1, $arr2);
	$merg = intarr($merg);
	$res = [];
	for ($i=0; $i < count($merg); $i++) { 
		$b = $merg[$i];
		$res[$b] = $i;
	}
	$res = array_flip($res);
	$res = intarr($res);
	return $res;
}

function arr_intersect($arr1, $arr2){
	$res = [];
	for ($i=0; $i < count($arr1); $i++) { 
		for ($j=0; $j < count($arr2); $j++) { 
			if($arr1[$i] == $arr2[$j]){
				$res[] = (int)$arr1[$i];
				break;
			}
		}
	}
	return $res;
}


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
					setcookie("user_id", $USER['id'], time()+3600*24*30*12, "/");
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
				setcookie("user_id", $USER['id'], time()-1, "/");
				setcookie("access_key", $USER['access_key'], time()-1, "/");
			}
		} 
	}
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

function get_btn_follow($userID){
	global $USER;
	$isFollow = count(array_intersect((array)json_decode($USER->follow), array($userID))) > 0 ? "__follow": "";
	$text = $isFollow == "__follow"? "Не следить": "Следить";
	if((int)$userID != $USER->id){ ?>
	<button userid="<?php echo $userID; ?>" onclick="btn_follow(this)" class="btn-follow <?php echo $isFollow; ?>"><?php echo $text; ?></button>
	<?php
	}

}

function toFollow($myID, $userID){
	global $db;
	$ret = (object)['type'=>'error'];
	$myID = (int)$myID;
	$userID = (int)$userID;
	$follow = false;
	$q = $db->query("SELECT follow from users where id = $myID limit 1");
	$row = mysqli_fetch_assoc($q);
	$q2 = $db->query("SELECT subscribers from users where id = $userID limit 1");
	if($row && $row2){
		$flw = json_decode($row['follow']);
		$flw2 = json_decode($row2['subscribers']);
		$ent = arr_intersect($flw, (array)$userID);
		if(count($ent) > 0){
			$flw = arr_diff($flw, (array)$userID);
			$flw2 = arr_diff($flw2, (array)$myID);
			$follow = false;
		}else{
			$flw = unite($flw, (array)$userID);
			$flw2 = unite($flw2, (array)$myID);
			$follow = true;
		}
		$flw = json_encode($flw);
		$flw2 = json_encode($flw2);
		$q3 = $db->prepare("UPDATE users set follow = ? where id = ? limit 1");
		$q3->bind_param("si", $flw, $myID);
		$q3->execute();
		$q4 = $db->prepare("UPDATE users set subscribers = ? where id = ? limit 1");
		$q4->bind_param("si", $flw2, $userID);
		$q4->execute();
		if($q3){
			$ret = (object)[
				'value'=> $follow ? "follow": "unfollow",
				'user_id'=>$userID,
			];
		}
	}
	return $ret;
}

function GetListTestsByUserId($id){
	global $db;
	$userID = (int)$userID;
	$res_user = false;
	$q = $db->query("SELECT * from tests where autor = $id order by date_created desc");
	while ($row = mysqli_fetch_assoc($q)){
		$resultArray[] = (object)$row;
	}
	return $resultArray;

}



?>