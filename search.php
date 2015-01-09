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
function mongoConnect(){
		$dbhost = 'localhost';
		$dbname = 'youtubeDB';
		$dbuser="youtube";
		$dbpasswd="tan";
		$mongoClient = new
				MongoClient("mongodb://localhost",
								array(
										'db' => $dbname,
									//	'username' => $dbuser,
									//	'password' => $dbpasswd
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

function mysqlSearch(){
		//$db = mongoConnect();
		$db = mysqlConnect();

		$searchphrase = $_POST['search'];
		$orderby = $_POST['order'];
		if($orderby==""){
		}
		else{
				$orderby= " order by ".$orderby." desc ";
				$orderby = mysqli_real_escape_string($db,$orderby);
		}
		//echo $_POST["search"];
		$searchphrase = mysqli_real_escape_string($db,$searchphrase);
		$table = "test";
		//$table = "record";
		$sql_search = "select * from ".$table." where ";
		$sql_search = $sql_search."title like('%".$searchphrase."%')";
		$sql_search .=$orderby;
		$sql_search .= "limit 10";
		//echo $sql_search;

		/*search all columns*/
		/*
		   $sql_search_fields = Array();
		   $sql = "SHOW COLUMNS FROM ".$table;
		   $rs = mysqli_query($db,$sql);
		   while($r = mysqli_fetch_array($rs)){
		   $colum = $r[0];
		   $sql_search_fields[] = $colum." like('%".$searchphrase."%')";
		   }

		   $sql_search .= implode(" OR ", $sql_search_fields);
		 */
		/**/
		/*mysql result to json*/
		$rs2 = mysqli_query($db,$sql_search);
		$rows = array();
		while($r = mysqli_fetch_assoc($rs2)) {
				$rows[] = $r;
		}
		print json_encode($rows);
		/**/
		$db->close();
}
function mongoSearch(){
		$db = mongoConnect();
//		$collection = $db->test;
		//$videoDB='test';
		$videoDB='record';
		$collection=$db->selectCollection("$videoDB");
		//$collection=db->record;
		$searchphrase = $_POST['search'];
		$page=1;
		if(isset($_POST['page'])){
				if($page >= 1 ){
						$page = $_POST['page'];
				}
		}
		$sortField='_id';
		//$sortField='viewCount';
		$limitCount=$page * 20;
		$limitCount=500;
		$skipCount=($page -1) * 20;
		//$regex = new MongoRegex('/.*'.$searchphrase.'.*/i');
		$ops = array(
						array(
								'$match' => array(
											'$text' => array(
													'$search' => $searchphrase
											)
										)
		//					 ),
		//				array(
		//						'$sort' => array(
		//								$sortField	=> -1 
		//								)
		//				),
		//				array(
		//						'$limit' => $limitCount
							 ),
						array(
								'$skip' => $skipCount
						)
				);
		$res = $ops;
		$i=1;
		if(isset($_POST['category'])){
				if($_POST['category'] != ""){
						$category=$_POST['category'];
						$res = array_merge($res ,
						[array('$match' => array( 'category' => $category) )] );
				//		array_slice($ops, $i, count($ops) - 1) ) ;
						}
		}
		if(isset($_POST['sortField'])){
				if($_POST['sortField'] != ""){
						$sortField=$_POST['sortField'];
						$res = array_merge($res ,
						[array('$sort' => [ $sortField => -1]  )]   ) ;
						}
		}
		$res = array_merge($res , [['$limit' => $limitCount]]);
		//$result= $collection->aggregate($ops);
		$options=[ 'cursor' => [ 'batchSize' => 0 ]];
		//$result= $collection->aggregate($ops,$options);
//		$result= $collection->aggregate([ ['$match'=>['$text' =>['$search'=>$searchphrase]]],['$limit' => $limitCount]],$options);
		$result= $db->command(['aggregate'=>$videoDB,'pipeline'=>$res],['timeout'=>60000000]);
		//$result->timeout(-1);
/*		foreach($result as $k => $row){
				echo json_encode($row);
				break;
		}*/
		if(isset($result['result'])){
				echo json_encode($result['result']);
		}
		else {
				var_dump($result);
		}
		//echo json_encode($result);
		//echo $result['result'][0]['_id']->{'$id'};
		//$a="";
		//$a=$result['result'][0]['_id'];
		//echo date('Y-m-d',$a->getTimestamp());
		//var_dump($a);
//		var_dump($result['result'][0]['_id']);
		//var_dump($result);
//		echo count($result['result']);
		return;	

/**/
//		$sort[] = array($orderby => -1);
//		$regex = new MongoRegex('/.*'.$searchphrase.'.*/i');
/*		$findQuery = array( '$or' => array( array('content' => $regex), array('title' =>   $regex ) ) );
		if($orderby==""){
				$result = $collection->find($findQuery);
		}
		else{
				$result = $collection->find($findQuery)->sort($sort);
		}
*/
/**/
		$result->timeout(-1);
		$notFirst=false;
		echo "[";
		foreach($result as $k => $row){
				if($notFirst == true){
						echo ',';
				}
				else{
						$notFirst=true;
				}
				echo json_encode($row);
				//echo $row['title'].'<br/>';

		}
		echo "]";
}

function mongoSearchG(){
		$db = mongoConnect();
		$videoDB='test';
		//$videoDB='record';
		$collection=$db->selectCollection("$videoDB");
		$searchphrase = $_GET['search'];
		$orderby = $_GET['order'];
		$page=1;
		if(isset($_GET['page'])){
				if($page >= 1 ){
						$page = $_GET['page'];
				}
		}
		$sortField='viewCount';
		if(isset($_GET['sortField'])){
				$sortField = $_GET['sortField'];
		}
		$limitCount=$page * 20;
		$skipCount=($page -1) * 20;
		//$regex = new MongoRegex('/.*'.$searchphrase.'.*/i');
		$ops = array(
						array(
								'$match' => array(
											'$text' => array(
													'$search' => $searchphrase
											)
										)
							 ),
						array(
								'$sort' => array(
										$sortField => -1
										)
						),
						array(
								'$limit' => $limitCount
							 ),
						array(
								'$skip' => $skipCount
						)
				);
		if(isset($_GET['category'])){
				$category=$_GET['category'];
				$res = array_slice($ops, 0, 1) +
						array ( 1 => array('$match' => array( 'category' => $category))) +
						array_slice($ops, 1, count($ops) - 1) ;
		}
		else{
				$res = $ops;
		}
		//$result= $collection->aggregate($ops);
		$result= $collection->aggregate($res);
		//		$result->timeout(-1);
/*		foreach($result as $k => $row){
				echo json_encode($row);
				break;
		}*/
		echo json_encode($result['result']);
		//echo $result['result'][0]['_id']->{'$id'};
		//$a="";
		//$a=$result['result'][0]['_id'];
		//echo date('Y-m-d',$a->getTimestamp());
		//var_dump($a);
//		var_dump($result['result'][0]['_id']);
		//var_dump($result);
//		echo count($result['result']);
		return;	

/**/
//		$sort[] = array($orderby => -1);
//		$regex = new MongoRegex('/.*'.$searchphrase.'.*/i');
/*		$findQuery = array( '$or' => array( array('content' => $regex), array('title' =>   $regex ) ) );
		if($orderby==""){
				$result = $collection->find($findQuery);
		}
		else{
				$result = $collection->find($findQuery)->sort($sort);
		}
*/
/**/
		$result->timeout(-1);
		$notFirst=false;
		echo "[";
		foreach($result as $k => $row){
				if($notFirst == true){
						echo ',';
				}
				else{
						$notFirst=true;
				}
				echo json_encode($row);
				//echo $row['title'].'<br/>';

		}
		echo "]";
}
?>
<?php
if(isset($_POST['method'])){
		if($_POST['method']== "mysql")
		{
				mysqlSearch();
		}
		else
		{
				mongoSearch();
		}
}
else{
		echo "should use post!";
}
?>
