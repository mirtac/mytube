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
		$dbhost = '140.123.101.185:27017';
		$dbname = 'youtubeDB';
		$dbuser="youtube";
		$dbpasswd="tan";
		$mongoClient = new
				MongoClient("mongodb://140.123.101.185:27017",
								array(
										'db' => $dbname,
										'username' => $dbuser,
										'password' => $dbpasswd
									 )
						   );
		$db = $mongoClient->$dbname;
		$collection="record";
		$c_record = $db->record;
		$record = $c_record->find();
		return $db;
}
?>
<?php
//ini_set('display_errors','On');
//error_reporting(E_ALL);

$newline="<br/>";

//$db = mongoConnect();
$db = mysqlConnect();

//$searchphrase = $_POST['search'];
$table = "test";
/*$sql_search = "select * from ".$table." where ";
$sql_search = $sql_search."title like('%".$searchphrase."%')";
$sql_search .= "limit 10";
*/
$sql_search = "select distinct category from record";
//$searchphrase = mysqli_real_escape_string($db,$searchphrase);

$rs2 = mysqli_query($db,$sql_search);
//$rs2 = mysqli_query($db,"SELECT * from record");
 /* $rows = array();
  while($r = mysqli_fetch_assoc($rs2)) {
		                    $rows[] = $r;
}
print json_encode($rows);
*/

$i=0;
  while($r = mysqli_fetch_assoc($rs2)) {
  		  $i++;
  		  print $r["category"];
  		  echo "\t";
  		  if($i%10 ==0)print "\n";

  }
?>
