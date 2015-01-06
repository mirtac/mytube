<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
function mysqlConnect(){
		$dbhost="localhost";
		$dbuser='youtube';
		$dbpasswd='tan';
		$dbname='youtubeDB';
		$db = new mysqli($dbhost,$dbuser ,$dbpasswd,$dbname);
		if($db->connect_errno > 0){
				die('Unable to connect to database [' . $db->connect_error . ']');
		}
		return $db;

}
if(is_array($_GET)&&count($_GET)>0){
//		$db = mysqlConnect();
		$table = 'test';
		extract($_GET);
		if($type=='insert'){
				$query = "INSERT INTO $table (id,title ,published,content,category,duration,favoriteCount,viewCount,author,keyword,uid) VALUES ('$id','$title',$published,'$content',$duration,$favoriteCount,$viewCount,'$author','$keyword',$uid )";
		}
		elseif($type=='update'){
				$query = "UPDATE $table SET title='$title',content='$content',category='$category',duration=$duration,author='$author',keyword='$keyword' WHERE id=$id";
		}
		elseif($type=='delete'){
				//TODO
		}
		else{
				echo 'something error';
				return;
		}
//		$query = mysqli_real_escape_string($db,$query);
		echo $query;
//		mysqli_query($db,$query);
}





?>

