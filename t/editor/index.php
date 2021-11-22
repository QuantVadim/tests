<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
include $_SERVER["DOCUMENT_ROOT"].'/basic.php';
include $ROOT."/t/question_adapter.php";
loginUser();

$Test = [];
$Questions = [];
//Получение теста
if(isset($_GET['id']) && $_GET['id'] == "new"){
    
}else if(isset($_GET['id'])){
        $test_id = (int)$_GET['id'];
        $query = $db->query("Select * from tests where id = $test_id");
    if($row = mysqli_fetch_assoc($query)){
        $Test = (object)$row;
        $arr = json_decode($row['data']);
        for ($i=0; $i < count($arr); $i++) { 
            $Questions[] = $arr[$i];
        }
    }        
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../styles/TestStyle.css">
    <script src="../../scripts/basic.js" type="text/javascript"></script>
    <script src="../../scripts/test_elements_editor.js" type="text/javascript"></script>
    <title>Тест</title>
</head>
<body>
<script type="text/javascript">
let test_id = "<?php echo $_GET['id']; ?>";     
</script>

<main class="_test">
    <div class="box">
        <content>
        <input class="_edit-test-name" value="<?php echo $Test->name ; ?>">
        </content>
    </div>
    <div class="test-wrapper _editor">
   <?php 
        if(isset($_GET['id']) && $_GET['id'] == "new"){

        }else if(isset($_GET['id']) ){

            for ($i=0; $i < count($Questions ); $i++) { 
                get_form_quest_editor($Questions [$i]);
            }
        }else{
            echo "Тест не существует";
        }  
   ?>
   </div>
    <div class="creators-quest">
        <div class="_header">
            <button onclick="CreateQuestForm('simple')">Вопрос-ответ</button>
            <button onclick="CreateQuestForm('choice')">Выбор</button>
        </div>
        <div class="_wrapper">
            <?php 

                for($i = 0; $i < count($objects_quest); $i++){
                    echo get_form_quest_editor($objects_quest[$i]);
                }

            ?>
        </div>
    </div>

   <button onclick="saveTest()">Сохранить</button>
   <form id="form-save" style="" method="POST" action="../">
        <input type="hidden" id="data-update" name="data-update"></input>
        <input type="hidden" id="data-test-id" name="id"></input>
        <input type="hidden" id="data-test-name" name="name"></input>
   </form>
</main>
<?php

?>
</body>
</html>