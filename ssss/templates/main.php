<?php 
include "questions/simple.php";
include "questions/choice.php";

function html_user_myTests($user_id){
	$user_id = (int)$user_id;
	$ret = "";
	$tests = GetListTestsByUserId($user_id);
        for ($i=0; $i < count($tests); $i++) { 
            $ret.=get_test_block($tests[$i]);
        }
    return $ret;
}

function html_user_myAnswers($userID){
	global $db;
	global $config;
	global $USER;
	$ret = "";
	$userID = (int)$userID;
	if($USER->id == $userID) $list = json_decode($USER->list);
	if(!$list){
		$q = $db->query("SELECT users.name 'user_name', users.login 'login', tests.name 'test_name', tests_results.answers 'answers', tests_results.id 'id', tests_results.date 'date' from tests_results left join users on users.id = tests_results.user_id left join tests on tests_results.test_id = tests.id where autor = $userID order by date desc");
		while($row = mysqli_fetch_assoc($q)){
		$ans = json_decode($row['answers']);
		$row = (object)$row;
		$corrects = 0;
		$wrongs = 0;
		for ($i=0; $i < count($ans); $i++) { 
			if($ans[$i]->grade > 0) $corrects++;
			else $wrongs++;
		}
		$link = $config->pages->result."?id=".$row->id;
		$header = $row->user_name." - ".$row->test_name;
		$us_login = $row->login;
		$date = $row->date;
 		$grade = $corrects."/".($wrongs+$corrects);
		$rowHTML ="
		<div class='node-element'>
			<h3><a href='$link'>$header</a></h3>
			<div>$us_login</div>
			<div>$date</div>
			<div>$grade</div>
		</div>";
		$ret.=$rowHTML;
		}
	}	
	return $ret;
}



function html_user_myResults($myID, $userID){
	global $db;
	global $config;
	$ret = "";
	$myID = (int)$myID; $userID = (int)$userID;
	$insertInQuery = "";
	if($myID != $userID){
		$insertInQuery = "AND tests_results.test_autor = $userID";
	}
	$q = $db->query("SELECT tests_results.id 'id', tests_results.answers 'answers', tests.name 'test_name', tests_results.date 'date', tests.description 'description' from tests_results left join tests on tests.id = tests_results.test_id where tests_results.user_id = $myID $insertInQuery order by tests_results.date desc");
	while($row = mysqli_fetch_assoc($q)){
		$ans = json_decode($row['answers']);
		$row = (object)$row;
		$corrects = 0;
		$wrongs = 0;
		for ($i=0; $i < count($ans); $i++) { 
			if($ans[$i]->grade > 0) $corrects++;
			else $wrongs++;
		}
			
		$link = $config->pages->result."?id=".$row->id;
		$test_name = $row->test_name;
		$description = $row->description;
		$date = $row->date;
		$grade = $corrects."/".($wrongs+$corrects);
		$rowHTML = "
		<div class='node-element'>
			<h3><a href='$link'>$test_name</a></h3>
			<div>$description </div>
			<div>$date</div>
			<div>$grade</div>
		</div>";
		$ret.=$rowHTML;
	}
	return $ret;
}



function html_user_mySubscribers($userID, $groupID = false){
	global $db;
	global $config;
	global $USER;
	$ret="";
	$userID = (int)$userID;
	$follow = false;
	if($USER->id == $userID) $follow = json_decode($USER->follow);
	if(!$follow){
		if(!$groupID || $groupID == 'all'){
			$q = $db->query("SELECT subscribers from users where id = '$userID' limit 1");
			if($row = mysqli_fetch_assoc($q)){
				$follow = (array)json_decode($row['subscribers']);
				$fst = str_replace(["[","]"], "", $row['subscribers']);
			}
		}else{
			$groupID = (int)$groupID;
			$q = $db->query("SELECT subscribers from groups where id = $groupID and autor = $userID limit 1");
			if($row = mysqli_fetch_assoc($q)){
				$follow = (array)json_decode($row['subscribers']);
				$fst = str_replace(["[","]"], "", $row['subscribers']);
			}
		}
	}
	if(count($follow) > 0){
		$q2 = $db->query("SELECT * FROM users where id in ($fst)");
		while($row = mysqli_fetch_assoc($q2)){
			//Формирование записи
			$usr = (object)$row;

			$user_id = $usr->id;
			$link = $config->pages->user."?id=".$usr->id;
			$user_name = $usr->name;
			$login = $usr->login;
			$funSele=$USER->id==$userID ? "userid='$user_id' onclick='selectSubscriber(this)'":"";
			$rowHTML = "
			<div class='node-element' $funSele>
				<h3><a onclick='null;' href='$link'>$user_name</a></h3>
				<h4>$login</h4>
			</div>";
			$ret.=$rowHTML;
		}
	}
	echo mysqli_error($db);
	return $ret;
}




function html_user_mySubscriptions($userID){
	global $db;
	global $config;
	global $USER;
	$ret = "";
	$userID = (int)$userID;
	$follow = false;
	if($USER->id == $userID) $follow = json_decode($USER->follow);
	if(!$follow){
		$q = $db->query("SELECT follow from users where id = $userID limit 1");
		if($row = mysqli_fetch_assoc($q)){
			$follow = (array)json_decode($row['follow']);
			$fst = str_replace(["[","]"], "", $row['follow']);
		}
	}
	if(count($follow) > 0){
		$q2 = $db->query("SELECT * FROM users where id in ($fst)");
		while($row = mysqli_fetch_assoc($q2)){
			//Формирование записи
			$usr = (object)$row;
				
			$link = $config->pages->user."?id=".$usr->id;
			$user_name = $usr->name;
			$user_login = $usr->login;
			$rowHTML = "
			<div class='node-element'>
				<h3><a href='$link'>$user_name</a></h3>
				<h4>$user_login</h4>
			</div>";
			$ret.=$rowHTML;
		}
	}
	return $ret;
}


function get_test_block($data){
	global $config;
	$ret="";
	$data = (object)$data;
	$name = $data->name;
	$description = $data->description;
	$link = $config->pages->test.'?id='.$data->id;
	$passed = "Пройдено раз: ".$data->passed_count;
	$ret = "
	<div class='test-block node-element'>
		<h3><a href='$link'>$name</a></h3>
		<div>$description</div>
		<div>$passed</div>
	</div>
	 ";
	 return $ret;
}


 ?>