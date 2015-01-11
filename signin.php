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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/bootstrap.min.css" rel="stylesheet" media="screen"> 
<script type="text/javascript" src="./js/jquery.min.js">
<script type="text/javascript" src="./js/jquery.masonry.min.js">      
<script src="./js/bootstrap.min.js"></script>
<script src="http://platform.twitter.com/widgets.js" async></script>
<link rel="stylesheet" type="text/css" href="style.css">
<meta charset="UTF-8">
</head>
<body>
<div class="signin">
<h1>SIGN IN</h1>
<a href="./create_account.php"><h3>create account?</h3></a>
<form method="post" action="handle.php" name="info">
<input type="text" name="account" class="input-xlarge" placeholder="account"></input><br>
<input type="password" placeholder="password" name="passwd"></input><br>
<input type="hidden" name="type" value="signin" />
<input type="submit" value="login" class="btn_my" name="submit"/>
<br>
</form>
</div>
</body>
</html>
