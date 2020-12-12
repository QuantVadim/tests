<?php 
	include $ROOT."/templates/questions/simple.php";
	include $ROOT."/templates/questions/choice.php";
$objects_quest = array(
	$object_quest_simple,
	$object_quest_choice
);

	function get_form_quest($data){
		switch ($data->type) {
			case 'simple':
				get_quest_simple($data);
				break;
			case 'choice':
				get_quest_choice($data);
			break;
			default:
				echo '<div class="quest-block">Ошибка: Вопрос не может быть отображен</div>';
				break;
		}
	}

	function get_form_quest_editor($data){
		switch ($data->type) {
			case 'simple':
				get_quest_simple_editor($data);
				break;
			case 'choice':
				get_quest_choice_editor($data);
			break;
			default:
				echo '<div class="quest-block">Ошибка: Вопрос не может быть отображен</div>';
				break;
		}
	}

	function get_form_quest_result($answer, $data){
		switch ($data->type) {
			case 'simple':
				get_quest_simple_result($answer, $data);
				break;
			case 'choice':
				get_quest_choice_result($answer, $data);
			break;
			default:
				echo '<div class="quest-block">Ошибка: Вопрос не может быть отображен</div>';
				break;
		}
	}
?>