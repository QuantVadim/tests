<?php 

$object_quest_simple = (object) array(
    "type" => "simple",
    "text" => "Текст",
    "answer" => ""
);

function get_quest_simple($data){
$text = $data->text;
$uid = $data->uid;

$result = "
<div class='quest-block' qtype='simple' uid='$uid'>
	<div class='_wrapper'>
	<p class='quest-text'>
	$text
	</p>
	<input class='_answer' type='text'>
	</div>
</div>";
 } 
return $result;
 ?>

<?php function get_quest_simple_editor($data){
	$text = $data->text;
	$answer = $data->answer;
	$uid = $data->uid;
$result = "
<div class='quest-block' qtype='simple' uid='$uid'>
	<div class='quest-header'>
		<button class='btn btn-del-quest' onclick='btn_deleteQuestBlock(this)'></button>
	</div>
	<div class='_wrapper'>
	<p class='quest-text' contenteditable='true'>
	$text	
	</p>
	<input class='_answer' type='text' value='$answer'>
	</div>
</div>";

return $result;
} ?>

<?php function get_quest_simple_result($answer, $data){
$text = $data->text;
$answert = $answer->answer;
$answerd = $data->answer;
$result = "
<div class='quest-block' qtype='simple' uid='<?php echo $data->uid ?>'>
	<div class='_wrapper'>
	<p class='quest-text'>
	 $text	
	</p>
	<h4 class='_answer' type='text'> $answert </h4>
	<div>
	$answerd
	</div>
	</div>
</div>
";
return $result;

} ?>