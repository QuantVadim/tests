<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
include $_SERVER["DOCUMENT_ROOT"].'/includes/basic.php';
include $ROOT."/test/question_adapter.php";
loginUser();

$Test = false;
$Questions = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../styles/style.css">
    <script src="../../scripts/bacsic.js" type="text/javascript"></script>
    <title>Тест</title>
</head>
<script type="text/javascript">
let result_id = "<?php echo $_GET['id']; ?>";     
</script>
<body>
<?php get_header(); ?>



<main class="_test">
   <?php
      if(strlen($_POST["data-answers"])>0 && strlen($_POST["id"])>0 ){
        $test_id = (int)$_POST["id"];
        $user_id = $USER->id;
        $res = CheckTest($test_id, $USER->id, $_POST["data-answers"] );
        if($res){
          $q = $connection->query("SELECT users.name 'user_name', users.id 'user_id', tests_results.answers 'answers', tests.data 'data' from tests_results left join tests on tests_results.test_id = tests.id left join users on users.id = tests_results.user_id where tests_results.user_id = $user_id order by tests_results.date desc limit 1");
            $test_result = mysqli_fetch_assoc($q);
        }
      }

   		$resultID = (int)$_GET['id'];
      if(!isset($test_result)){
   		$q = $connection->query("SELECT users.name 'user_name', users.id 'user_id', tests_results.answers 'answers', tests.data 'data', tests.name 'test_name' from tests_results left join tests on tests_results.test_id = tests.id left join users on users.id = tests_results.user_id where tests_results.id = $resultID limit 1");
   		 $test_result = mysqli_fetch_assoc($q);
      }
      ?>
      <div class="box">
        <content>
        <h2>
          <?php echo $test_result['test_name']; ?>
        </h2>
         <div>
          Тест проходил: <a href="<?php echo $config->pages->user.'?id='.$test_result['user_id']; ?>"><?php echo $test_result['user_name']; ?></a>
        </div>
        </content>
      </div>

      <?php
      if($test_result){
   			$usrAns = json_decode($test_result['answers']);
   			$data = json_decode($test_result['data']);
   			for ($i=0; $i < count($usrAns); $i++) { 
   				for ($j=0; $j < count($data); $j++) { 
   					if($usrAns[$i]->uid == $data[$j]->uid){
   						get_form_quest_result($usrAns[$i], $data[$j]);
   						break;
   					}
   				}
   			}
   		}
        echo mysqli_error($connection);
   ?>
    </div>
    <?php if($TypePage == 'default'){?>
        <button onclick="sendTest()">Отправить</button>
    <?php } ?>
</main>
<form id="test-send" method="POST" action="">
    <input id="data-test-id" type="hidden" name="id">
    <input id="data-answers" type="hidden" name="data-answers">
</form>
<?php
get_login_form();
?>
</body>
</html>