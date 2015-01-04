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
$videoDB = 'test';
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
				var_dump($_POST);
				//extract( $_POST );
				$account= $_POST['account'];
				$passwd = $_POST['passwd'];
				checktext($account);
				checktext($passwd);
				
				$db = mongoConnect();
				$collection=$db->selectCollection("userDB");
				$loginQuery = [ 'account' => $account , 'passwd' => $passwd ] ;


				$result = $collection->findOne($loginQuery);
				if($result!=null){//login successful
						$_SESSION["uid"]=$result['_id'];
						$_SESSION["account"]=$result['account'];
						$_SESSION["passwd"]=$result['passwd'];
						$_SESSION["email"]=$result['email'];
						$_SESSION["name"]=$result['name'];
						
						echo '<script>document.location.href="./display.php"</script>';
				}
				else {//TODO error account or passwd;
//				echo "'$account' have not been registered    <a href='./create_account.php'>CREATE IT?</a><br/>";
						echo 'login fail!! <a href="signin.php">SIGNIN</a>';
				}
				return;
		}
		elseif($type=='manageVideo'){//TODO
				if($type=='insert'){//TODO
						$vid= $_POST['vid'];
						$title = $_POST[''];
						$published = $_POST[''];
						$content = $_POST[''];
						$category = $_POST[''];
						$duration = $_POST[''];
						$favoriteCOunt = $_POST[''];
						$viewCount = $_POST[''];
						$author = $_POST[''];
						$keyword = $_POST[''];
						$uid = $_POST[''];
						$tag = 'tag';
						$dislike= 'dislike';
						$db = mongoConnect();
						$collection=$db->selectCollection("$videoDB");
						$result = $collection->find(array('vid' => $vid))->count();


						if($result!=0){//TODO  this is create account
								echo '<script>document.location.href="./error.php?errno=1"</script>';
						}
						else{
								$doc = [ "account" => $account , "email" => $email , "passwd" => $passwd , 'name' => $name];
								$collection->insert($doc);
								echo 'successful';
						}

						$query = "INSERT INTO $table (id,title ,published,content,category,duration,favoriteCount,viewCount,author,keyword,uid) VALUES ('$id','$title',$published,'$content',$duration,$favoriteCount,$viewCount,'$author','$keyword',$uid )";
				}
				elseif($type=='update'){
						$query = "UPDATE $table SET title='$title',content='$content',category='$category',duration=$duration,author='$author',keyword='$keyword' WHERE id=$id";
				}
				elseif($type=='delete'){
						//TODO
				}
		}
		elseif ($type=='comment'){
				//echo 'text='.$textpost.'<br/>';
				//echo 'name='.$name.'<br/>';
				//echo 'aid='.$aid.'<br/>';
				//echo 'mid='.$mid.'<br/>';
				$db = mongoConnect();
				$collection=$db->selectCollection("commentDB");
				if(isSet($_POST['message'])){//TODO insert message to commentDB
						if($_POST['message']==''){
								break ;
						}
						$query = [ 
								'uid'=> $_SESSION['uid'] ,
								'vid' => $_POST['vid'] ,
								'content' => $_POST['message'],
								'name' => $_SESSION['name']
										] ;
//						var_dump($query);
						try{
								$collection->insert($query);
						}catch(Exception $e){
								echo "comment insert fail";
						}

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
						echo '{"name":"'.$row['name'].'","content":"'.$row['content'].'","time":"'.date('Y-m-d',$row['_id']->getTimestamp()).'","cid":"'.$row['_id'].'","vid":"'.$row['vid'].'"}';
						//echo json_encode($row);
				}
				echo ']';


				return;
		}
		else{
				echo "undifine";
		}
}
elseif (count($_GET>0)){
//		extract( $_GET );
//TODO check is login    //all thing in get must login
		$type = $_GET['type'];
		if(isset($_SESSION['uid'])){
		}
		else return false;
		if ($type=='mlike'){//NOT USE
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect(112): ' . mysql_error()); 
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
		elseif ($type=='clike'){//NOT USE
				$link = mysql_connect('localhost', 's499410039','sql321'); 
				if(!$link) { 
						die('Could not connect: (51)' . mysql_error()); 
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
		elseif($type=='logout'){//TODO dup logout
				/*logout!*/

				var_dump($_SESSION);
				echo '<br/>';
				foreach ($_SESSION as $i => $value) {
						unset($_SESSION[$i]);
				}
				//echo '['.$_SESSION['uid'].']';
				/*logout!*/
				
				echo $_SESSION['name'].'logout<br>';
				echo 'turn to sign in 1 sec<br>';
				echo '<script>setTimeout(function() { document.location.href="./signin.php";}, 2000);</script>';
			

		}
		elseif($type=='getlist'){
				$limitCount=20;
				$skipCount=0;
				//TODO check whether isSet ($_GET)[$which,$title,$vid]
				$which = $_GET['which'];
				if($which=='history'){
						$which .= 'ListDB';
				}
				else{
						echo "something wrong at getList";
				}
				$db = mongoConnect();
				$collection=$db->selectCollection($which);
				$loginQuery = [ 'uid' => $_SESSION['uid']   ] ;
				$ops = array(
								array(
										'$match' => array(
												'uid' => $_SESSION['uid']
												)
									 ),
								array(
										'$limit' => $limitCount
									 ),
								array(
										'$skip'  => $skipCount
								)
							);

				$result= $collection->aggregate($ops);
				echo json_encode($result['result']);


		}
		elseif($type=='addToList'){
				$limitCount=20;
				$skipCount=0;
				$which = $_GET['which'];
				//TODO check whether isSet ($_GET)[$which,$title,$vid]
				if($which=='history'||$which=='upload' || $which=='favorite'){
						$which .= 'ListDB';
				}
				else{
						echo "something wrong at getList";
						return;
				}
				$db = mongoConnect();
				$collection=$db->selectCollection($which);
						
				$title = $_GET['title'];
				$title = substr($title,0,15).((strlen($title)>15)?"...":"");
				$doc = [ "uid" => $_SESSION['uid'] , "vid" => $_GET['vid'] , "title" => $title];
				try{
						$collection->insert($doc);
				}
				catch(Exception $e){
						echo 'dupicate';
				}
				return;

		}
		elseif($type=='deleteComment'){
				$db = mongoConnect();
				$collection=$db->selectCollection('commentDB');
				$cid=new MongoId($_GET['cid']);
				$doc = [ "uid" => $_SESSION['uid'] , "vid" => $_GET['vid'] , "_id" => $cid];
				try{
						$collection->remove($doc);
//						print_r($doc);
				}
				catch(Exception $e){
						echo 'delete fail';
						print_r($doc);
				}
				
				$commentGetQuery = [ 'vid' => $_GET['vid'] ] ;

				$result = $collection->find($commentGetQuery);
				$result->timeout(-1);
				$isFirst=true;
				echo '[';
				foreach($result as $k => $row){
						if(!$isFirst){
								echo ',';
						}
						else {$isFirst=false;}
						echo '{"name":"'.$row['name'].'","content":"'.$row['content'].'","time":"'.date('Y-m-d',$row['_id']->getTimestamp()).'","cid":"'.$row['_id'].'","vid":"'.$row['vid'].'"}';
						//echo json_encode($row);
				}
				echo ']';


				return;

				return;

		}
		else{
				echo 'not define get method';
				//	echo '<script>document.location.href="./wall.php"</script>';
		}

}
else{
		echo '<script>document.location.href="./signin.php"</script>';
}

/*?>
</body>
</html>
*/

?>
