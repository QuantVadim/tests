<?php 
    include "config.php";
    include "basic.php";
    loginUser();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
	  <center>
	  	 <?php echo $USER->name; ?>
    	<form method="POST" action="user/">
   		<input type="text" name="login">
   		<br>
		<input type="password" name="password">
	    <br>
	    <input type="submit" name="">
    </form>
    </center>
    
    <?php echo mysqli_error($db); ?>
</body>
</html>