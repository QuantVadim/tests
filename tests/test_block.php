<?php

function get_test_block($data){
global $config;
$ret= "";
$data = (object)$data;
$name = $data->name;
$description = $data->description;
$link = $config->pages->test.'?id='.$data->id;
$passed = "Пройдено раз: ".$data->passed_count;
$ret = "
<div class='test-block node-element'>
	<h3><a href='$link''>$name</a></h3>
	<div>$description</div>
	<div>$passed</div>
</div>";
return $ret;
}

?>