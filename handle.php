<?php
session_start();
extract( $_POST );
function checktext($text){//check if '' or sql attack;   not done;
//TODO for mongoDB

/*		if($text=='')header("Location:error.php");
		if(strstr($text,' '))header("Location:error.php");
		if(strstr($text,'*'))header("Location:error.php");
*/		
}
function mongoConnect(){
		$dbhost = 'localhost';
		$dbname = 'youtubeDB';
		$dbuser="youtube";                                     
		$dbpasswd="tan";
		$mongoClient = new                                     
				MongoClient("mongodb://localhost",
								array(                         
										'db' => $dbname,
										//  'username' => $dbuser,
										//  'password' => $dbpasswd
									 )
						   );
		$db = $mongoClient->$dbname;
		/*search*//*
					 $collection="record";
					 $c_record = $db->record;
					 $record = $c_record->find();
				   *//**/
		return $db;
}
/*?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
*/
if(count($_POST)>0){

		//if(!strcmp($_POST["type"],"create")){
		if($type=='create'){
				$account= $_POST['account'];
				$passwd = $_POST['passwd'];
				$email = $_POST['email'];
				$name = $_POST['name'];
				checktext($account);
				checktext($passwd);
				checktext($email);
				
				
				$db = mongoConnect();
				$collection=$db->selectCollection("userDB");
				$result = $collection->find(array('account' => $account))->count();


				if($result!=0){//alert already have the same account;
						echo '<script>document.location.href="./error.php?errno=1"</script>';
				}
				else{
						$doc = [ "account" => $account , "email" => $email , "passwd" => $passwd , 'name' => $name];
						$collection->insert($doc);
						echo 'successful';
		

				}


//				mysql_close($link);
				echo '<script>document.location.href="./signin.php"</script>';
		}
		elseif ($type=='signin'){
				//extract( $_POST );
				$account= $_POST['account'];
				$passwd = $_POST['passwd'];
				checktext($account);
				checktext($passwd);
				
				$db = mongoConnect();
				$collection=$db->selectCollection("userDB");
				$loginQuery = [ 'account' => $account , 'passwd' => $passwd ] ;


				$result = $collection->findOne($loginQuery);
				if($result!=null){
//						var_dump( $result  );
						$_SESSION["uid"]=$result['_id'];
						$_SESSION["account"]=$result['account'];
						$_SESSION["passwd"]=$result['passwd'];
						$_SESSION["email"]=$result['email'];
						$_SESSION["name"]=$result['name'];
						
						/*logout!*/

						/*var_dump($_SESSION);
						echo '<br/>';
						foreach ($_SESSION as $i => $value) {
								    unset($_SESSION[$i]);
						}
						var_dump($_SESSION);
						echo '['.$_SESSION['uid'].']';*/
						/*logout!*/
						echo '<script>document.location.href="./display.php"</script>';
				}
				else {
//				echo "'$account' have not been registered    <a href='./create_account.php'>CREATE IT?</a><br/>";
						echo 'login fail!! <a href="signin.php">SIGNIN</a>';
				}
				return;


				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: ' . mysql_error()); 
				} 
				mysql_select_db("s499410039", $link);


				$queryString="select * from account where (name='$name'&&password='$passwd')";
				//				echo $queryString;
				$result = mysql_query($queryString);
				$row = mysql_fetch_array($result);
				if(count($row) >1 ){//get data
						//set session
						session_register("uid");
						session_register("account");
						session_register("passwd");
						session_register("email");
						$_SESSION["uid"]=$row['aid'];
						$_SESSION["account"]=$row['account'];
						$_SESSION["passwd"]=$row['password'];
						$_SESSION["email"]=$row['email'];
						
						//set cookie
//						setcookie("name",$name,time()+3600);
						echo '<script>document.location.href="./wall.php"</script>';
				}
				else{//error account or passwd;
						$queryString="select * from account where name='$name'";
						//				echo $queryString;
						$result = mysql_query($queryString);
						//echo $queryString;
						$row = mysql_fetch_array($result);
						if(count($row) >1 ){//get data
								echo '<script>document.location.href="./error.php?errno=3"</script>';
						}
						else{
								echo "'$account' have not been registered    <a href='./create_account.php'>CREATE IT?</a><br/>";
								echo 'or come back to <a href="signin.php">SIGNIN</a>';
						}
				}


		}
		elseif($type=='logout'){//session_unset();or: unset($_SESSION["XXX"]);
				session_unset();
				session_destroy();
				echo $_SESSION["account"].'logout<br>';
				echo 'turn to sign in 1 sec<br>';
				echo '<script>setTimeout(function() { document.location.href="./signin.php";}, 1000);</script>';

		}
		elseif ($type=='post'){
				$textpost=str_replace ("\r\n","<br/>",$textpost);
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: ' . mysql_error()); 
				} 
				mysql_select_db("s499410039", $link);


				$queryString="insert into message (content,aid) values ('$textpost',$aid)";
				//echo $queryString;
				$result = mysql_query($queryString);
				echo '<script>document.location.href="./wall.php"</script>';
		}
		elseif ($type=='comment'){
				//echo 'text='.$textpost.'<br/>';
				//echo 'name='.$name.'<br/>';
				//echo 'aid='.$aid.'<br/>';
				//echo 'mid='.$mid.'<br/>';
				$db = mongoConnect();
				$collection=$db->selectCollection("commentDB");
				if(isSet($_POST['message'])){//TODO insert message to commentDB
						$query = [ 
								'uid'=> $_SESSION['uid'] ,
								'vid' => $_POST['vid'] ,
								'content' => $_POST['message'],
								'name' => $_SESSION['name']
										] ;
//						var_dump($query);
						$collection->insert($query);

				}
				//TODO echo json for comment wall;
				$commentGetQuery = [ 'vid' => $_POST['vid'] ] ;

				$result = $collection->find($commentGetQuery);
				$result->timeout(-1);
				$isFirst=true;
				echo '[';
				foreach($result as $k => $row){
						if(!$isFirst){
								echo ',';
						}
						else {$isFirst=false;}
						echo '{"name":"'.$row['name'].'","content":"'.$row['content'].'","time":"'.date('Y-m-d',$row['_id']->getTimestamp()).'"}';
						//echo json_encode($row);
				}
				echo ']';


				return;
				$textpost=str_replace ("\r\n","<br/>",$textpost);
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: ' . mysql_error()); 
				} 
				mysql_select_db("s499410039", $link);


				$queryString="insert into comment (comment,aid,mid) values ('$textpost',$aid,$mid)";

				//echo $queryString;
				$result = mysql_query($queryString);



				/*$queryString="select * from message where mid=$mid";
				$result = mysql_query($queryString);
				$row=mysql_fetch_array($result);
				$queryString="select * from account where aid=".$row["aid"];
				$result = mysql_query($queryString);
				$row=mysql_fetch_array($result);
				$subject="some reply you";
				$mes="$name reply you!!";
				$tmp=mail($row["email"],$subject,$mes,"From: 499410039\n");
				echo $row["email"].$subject.$mes."From: 499410039\n";*/
				echo '<script>document.location.href="./wall.php"</script>';
		}
		else{
				echo "undifine";
		}
}
elseif (count($_GET>0)){
		extract( $_GET );
		if ($type=='mlike'){
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: ' . mysql_error()); 
				} 
				mysql_select_db("s499410039", $link);

				$queryString="select * from malike where mid=$mid&&aid=$aid";
				echo $queryString;
				$result = mysql_query($queryString);
				$datacount = mysql_num_rows($result);
				if($datacount!=0);
				else {
						$like=$like+1;
						$queryString="insert into malike (mid,aid) values($mid,$aid)";
						$result = mysql_query($queryString);
						//echo $queryString;
						$queryString="update message set plike=$like where mid=$mid";
						//echo $queryString;
						$result = mysql_query($queryString);
				}

		}
		elseif ($type=='clike'){
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: ' . mysql_error()); 
				} 
				mysql_select_db("s499410039", $link);

				$queryString="select * from calike where cid=$cid&&aid=$aid";
				echo $queryString;
				$result = mysql_query($queryString);
				$datacount = mysql_num_rows($result);
				if($datacount!=0);
				else {
						$like=$like+1;
						$queryString="insert into calike (cid,aid) values($cid,$aid)";
						$result = mysql_query($queryString);
						//echo $queryString;
						$queryString="update comment set clike=$like where cid=$cid";
						//echo $queryString;
						$result = mysql_query($queryString);
				}
		}
		echo '<script>document.location.href="./wall.php"</script>';
}
else{
		echo '<script>document.location.href="./signin.php"</script>';
}

/*?>
</body>
</html>
*/

?>
