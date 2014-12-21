<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<meta charset="UTF-8">
</head>
<body class="signin">
<h1>create_account.php</h1>
<a href="./signin.php"><h3>SIGN IN?</h3></a>
<form method="post" name="info" action="handle.php">
<input type="text" name="account" placeholder="account"></input><br>
<input type="password" name="passwd" placeholder="password"></input><br>
<input type="text" name="email" placeholder="email"></input><br>
<input type="text" name="name" placeholder="name"></input><br>
<input type="hidden" name="type" value="create" />
<input type="submit" />
<input type="reset" /><br>
</form>

</body>
</html>
