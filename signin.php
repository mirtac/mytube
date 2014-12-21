<?php
session_start();
if(isSet($_SESSION["uid"])){
		header("Location:display.php");
		exit;
}
extract($_COOKIE);
?>  


<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<meta charset="UTF-8">
</head>
<body>
<div class="signin">
<h1>SIGN IN</h1>
<a href="./create_account.php"><h3>create account?</h3></a>
<form method="post" action="handle.php" name="info">
<input type="text" name="account" placeholder="account"></input><br>
<input type="password" placeholder="password" name="passwd"></input><br>
<input type="hidden" name="type" value="signin" />
<input type="submit" value="submit" name="submit"/>
<input type="reset" /><br>
</form>
</div>
</body>
</html>
