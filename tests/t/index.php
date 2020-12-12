<?php
include $_SERVER["DOCUMENT_ROOT"]."/config.php";
include $_SERVER["DOCUMENT_ROOT"].'/basic.php';
include "question_adapter.php";
loginUser();

$Test = false;
$Questions = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/TestStyle.css">
    <script src="../scripts/basic.js" type="text/javascript"></script>
    <script src="../scripts/test_elements.js" type="text/javascript"></script>
    <title>Тест</title>
</head>
<script type="text/javascript">
let test_id = "<?php echo $_GET['id']; ?>";     
</script>
<body>

<?php  ?>
<?php
    $TypePage = "default";
    if(strlen($_POST["data-update"])>0){
        $dt = $_POST["data-update"];
        $name = $_POST['name'];
        $autor = $USER->id;
        if(isset($_POST['id']) && $_POST['id'] == "new"){
            $q = $db->prepare("INSERT into tests (name, data, autor) values(?, ?, ?)");
            $q->bind_param('ssi', $name, $dt, $autor);
            $q->execute();
            if($q){
                $q2 = $db->query("SELECT * from tests where autor = $autor limit 1");
                if($row = mysqli_fetch_assoc($q2)){
                    $Test = (object)$row;
                    $Questions = json_decode($Test->data);
                }
            }
        }else{
            $numb = (int)$_POST['id'];
            $q = $db->prepare("update `tests` set `data` = ? where `id` = ?");
            $q->bind_param('si', $dt, $numb);
            $q->execute();
        }
    
    }else if(isset($_GET['id'])){
        $test_id = $_GET['id'];
        $query = $db->query("Select * from tests where id = $test_id");
        if($row = mysqli_fetch_assoc($query)){
            $Questions = json_decode($row['data']);
            $Test = (object)$row;
        }
    }
    echo mysqli_error($db);
?>


<main class="_test">
    
   <?php 
    switch ($TypePage) {
        case 'default':
            ?>
            <div class="box">
                <content>
                <h2><?php echo $Test->name; ?></h2>
                <?php if($row['autor'] == $USER->id){?>
                <a href="editor/?id=<?php echo $row['id']; ?>">Редактировать</a>
                <?php } ?>
                </content>
            </div>
            <div class="test-wrapper">
            <?php
            if(isset($_GET['id'])){
                $arr = json_decode($row['data']);
                for ($i=0; $i < count($Questions); $i++) { 
                    get_form_quest($Questions[$i]);
                }
            }
            break;
        default:
            break;
    }
        
   ?>
    </div>
    <?php if($TypePage == 'default'){?>
        <button onclick="sendTest()">Отправить</button>
    <?php } ?>
</main>
<form id="test-send" method="POST" action="result/">
    <input id="data-test-id" type="hidden" name="id">
    <input id="data-answers" type="hidden" name="data-answers">
</form>
<?php

?>
</body>
</html>