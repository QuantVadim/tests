<?php 
$object_quest_choice =  (object)array(
        "type" => "choice",
        "text" => "Текст",
     	"multiselect" => "true",
     	"isRow" => "false",
        "answer" => [],
        "choice" => []
    );

function get_quest_choice($data){

$text = $data->text;
$multiselect = $data->multiselect;
$isRow = $data->isRow;
$uid = $data->uid;

$isRow = $data->isRow == "true" ? "__isrow" : "";
$innerChoice = ""; 
$ansHTML = array();
		$ans = $data->answer;
		for ($i=0; $i < count($ans); $i++) { 
			array_push($ansHTML, "<div class='el-choice-li'><button class='el-choice-li_btn'>".$ans[$i]."</button></div>");
		}
		$cho = $data->choice;
		for ($i=0; $i < count($cho); $i++) { 
			array_push($ansHTML, "<div class='el-choice-li'><button class='el-choice-li_btn'>".$cho[$i]."</button></div>");
		}
		shuffle($ansHTML);
		for ($i=0; $i < count($ansHTML); $i++) { 
			$innerChoice.=$ansHTML[$i];
		}
$result = "
<div class='quest-block' qtype='choice' uid='$uid'>
	<div class='_wrapper'>
	<p class='quest-text'>
	$text
	</p>
	<div class='el-choice $isRow' multiselect='$multiselect'>
	$innerChoice 
	</div>
	</div>
</div>
";
return $result;
} ?>


<?php function get_quest_choice_editor($data){

$text = $data->text;
$multiselect = $data->multiselect;
$isRow = $data->isRow;
$uid = $data->uid;

$isRow = $data->isRow == "true" ? "__isrow" : "";

$innerChoice = "";
$ansHTML = array();
$ans = $data->answer;
if(isset($ans))
for ($i=0; $i < count($ans); $i++) { 
	array_push($ansHTML, "<div class='el-choice-li' check='true'><button class='el-choice-li_btn'>".$ans[$i]."</button><button class='_drop'> X </buton></div>");
}
$cho = $data->choice;
if(isset($cho))
for ($i=0; $i < count($cho); $i++) { 
	array_push($ansHTML, "<div class='el-choice-li'><button class='el-choice-li_btn'>".$cho[$i]."</button><button class='_drop'> X </buton></div>");
}
for ($i=0; $i < count($ansHTML); $i++) { 
	$innerChoice.=$ansHTML[$i];
}

$result = "
<div class='quest-block' qtype='choice' uid='$uid'>
	<div class='quest-header'>
		<button class='btn btn-del-quest' onclick='btn_deleteQuestBlock(this)'></button>
	</div>
	<div class='_wrapper'>
	<p class='quest-text' contenteditable='true'>
	 $text	
	</p>
	<div class='el-choice $isRow' multiselect='$multiselect'>
		$innerChoice
	</div>
	<hr>
	<div class='quest-footer-editor'>
	<input type='text' class='el-choice_add-text-choice'><button class='_btn-add'>Добавить</button>
	<div class='hidden-list'>
		<button class='_btn-show'></button>
		<div class='_content'>
			<button class='check-box _param-isrow' ischeck='$isRow' onclick='checkbox(this)''>Вертикально</button>
			<button class='check-box _param-multiselect' ischeck='$multiselect' onclick='checkbox(this); btn_multiselect(this)'>Мультивыбор</button>
		</div>
	</div>
	</div>
	</div>
</div>";
	return $result;
} 
?>


<?php function get_quest_choice_result($answer, $data){
$text = $data->text;
$multiselect = $data->multiselect;
$isRow = $data->isRow;
$uid = $data->uid;

$isRow = $data->isRow == "true" ? "__isrow" : "";
$innerChoice = "";
$ansHTML = array();
$ans = (array)$data->answer;
if(isset($ans))
for ($i=0; $i < count($ans); $i++) {
	$arr_corrects = array_intersect((array)$ans[$i], $answer->answer);
	if(count($arr_corrects) > 0)
		$ansHTML[] = "<div class='el-choice-li __correct' check='true'><button class='el-choice-li_btn '>".$ans[$i]."</button></div>";
	else $ansHTML[] = "<div class='el-choice-li' check='true'><button class='el-choice-li_btn'>".$ans[$i]."</button></div>";
	}
$cho = $data->choice;
if(isset($cho))
for ($i=0; $i < count($cho); $i++) { 
	$arr_wrongs = array_intersect((array)$cho[$i], $answer->answer);
	if(count($arr_wrongs) > 0){
		$ansHTML[]= "<div class='el-choice-li __wrong'><button class='el-choice-li_btn'>".$cho[$i]."</button></div>";
	}
	else $ansHTML[]= "<div class='el-choice-li'><button class='el-choice-li_btn'>".$cho[$i]."</button></div>";
	}
for ($i=0; $i < count($ansHTML); $i++) { 
	$innerChoice.=$ansHTML[$i];
}

$result = "
<div class='quest-block' qtype='choice' uid='$uid'>
	<div class='_wrapper'>
	<p class='quest-text'>
	$text
	</p>
	<div class='el-choice $isRow' multiselect='$multiselect'>
	$innerChoice 
	</div>
	</div>
</div>
";
return $result;
 } 


 ?>
