<?php 
    include "../config.php";
    include "../basic.php";

    loginUser();

    $curUser = false;
    $useridGET = (int)$_GET['id'];
    $isMe = false;
    if($useridGET == $USER->id){
    	$curUser = $USER;
    	$isMe = true;
    }else if(isset($useridGET)){
    	$curUser = getUserbyId($useridGET);
    }else{
    	$curUser = $USER;
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title><?php echo $USER->name; ?></title>
    <script type="text/javascript" src="../scripts/basic.js"></script>
    <script type="text/javascript" src="../scripts/user.js"></script>
    
</head>
<body>
<script type="text/javascript">
	const curUserID = "<?php echo $curUser->id; ?>";
</script>
	<header>
		<div class="_wrapper">
			<div>
			<div class="logo"></div>
			</div>
			<div>

			</div>
			<div>
				<span><?php echo $USER->name; ?></span>
				<button class="btn-logout" onclick="logout()">Выход</button>
			</div>
		</div>
	</header>
	<main class="columns2">
	<content>
		<aside class="box _navigator">
		<?php if($curUser->id == $USER->id){?>
			<button onclick="SwitchPage('my_tests')" class="btn">Мои тесты</button>
			<button onclick="SwitchPage('my_answers')" class="btn">Ответы</button>
			<button onclick="SwitchPage('my_results')" class="btn">Мои решения</button>
			<button onclick="SwitchPage('my_subscriptions')" class="btn">Мои подписки</button>
			<button onclick="SwitchPage('my_subscribers')" class="btn">Мои Подписчики</button>
		<?php }else{?>
			<button class="btn">Моя страница</button>
			<button onclick="SwitchPage('my_tests')" class="btn">Тесты</button>
			<button onclick="SwitchPage('my_results')" class="btn">Мои решения</button>
			<button onclick="SwitchPage('my_subscriptions')" class="btn">Подписки</button>
			<button onclick="SwitchPage('my_subscribers')" class="btn">Подписчики</button>
		<?php } ?>
		</aside>
	</content>
	<content>
		<div class="box">
			<div class="box-wrapper">
			<h2><?php echo $curUser->name; ?></h2>
			<h3><?php echo $curUser->login; ?></h3>
			</div>
		</div>
		<div class="box">
			<div class="tool-box">
				<h2 class="_name-page">Тесты</h2>
				<?php if($isMe){?>
				<div class="_tools" name="my_tests">
				<a class="btn-link" href="">Создать тест</a>	
				</div>
				<div class="_tools" name="my_answers">
					<div class="_subboard">
						<button gid="all">Все</button>
					</div>
				</div>
				<?php } ?>	
			</div>
			<div class="box-wrapper">
			<div class="ajax-pages"></div>
			</div>
		</div>
	</content>
	</main>
<form id="form-logout" method="POST" action="../" style="display: none">
	<input type="text" name="logout" value="true">
	<input type="submit" name="">
</form>
</body>
</html>