<?php
session_start();
?>  

<?php 
switch($_GET["errno"]){
		case 1:$errmsg='The account name has been already register!!';break;
		case 2:$errmsg='permission deny!';break;
		case 3:$errmsg='Fail connection<br/>your account or password is invalid<br/>';break;
		default:$errmsg='some thing error';
		//2 name or passwd error
		//3 promission deny
}

session_unset();  
session_destroy();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
</head>
<body>
<h1>error!!!</h1>
<p>
<h1><?php echo $errmsg?></h1>
<script>setTimeout(function() { document.location.href="./signin.php";}, 1500);</script>
</p>

</body>
</html>
