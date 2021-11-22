<?php 
include "config.php";
include "basic.php";
include "templates/main.php";
$USER = false;
$ddd = 'delete me!';

if(isset($_POST['key']) && isset($_POST['myid'])){
	$access_key = $_POST['key'];
	$user_id = $_POST['myid'];
	$q = $db->query("SELECT users.id 'id', users.email 'email', users.login 'login', users.name 'name', users.image 'image', access_keys.access_key 'access_key' from access_keys left join users on access_keys.user_id = users.id where access_keys.user_id = '$user_id' and access_keys.access_key = '$access_key' limit 1");
	if($row = mysqli_fetch_assoc($q)){
		$USER = (object)$row;
	}
}

if($USER && ($obj = json_decode($_POST['object'])) ){
	switch ($obj->type) {
		case 'get_user_page':
			switch ($obj->page_name) {
				case 'my_tests':
					$res = [
						'type'=>'html',
						'name'=>'my_tests',
						'html'=>html_user_myTests($USER->id)
					];
					break;
				case 'my_results':
					$res = [
						'type'=>'html',
						'name'=>'my_results',
						'html'=>html_user_myResults($USER->id)
					];
					
					break;
				case 'my_subscriptions':
					$res = [
						'type'=>'html',
						'name'=>'my_subscriptions',
						'html'=>html_user_mySubscriptions((int)$obj->user_id)
					];
					break;
				case 'my_subscribers':
					$res = [
						'type'=>'html',
						'name'=>'my_subscribers',
						'html'=>html_user_mySubscribers((int)$object->user_id)
					];
					break;
				case 'my_answers':
					$res = [
						'type'=>'html',
						'name'=>'my_answers',
						'html'=>html_user_myAnswers((int)$USER->id)
					];
					break;
			}
			echo json_encode((object)$res);
			break;
		case 'action':
			switch ($obj->name) {
				case 'tofollow':
					$res = toFollow($obj->myid, $obj->user_id);
					break;
					
				default:
					$res = ['type'=>'error'];
					break;
			}
			echo json_encode((object)$res);
			break;
		
		default:
			
			break;
	}


}else{
	echo "ERR";
}


 ?>