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

//phpinfo();
//$dbhost="140.123.101.185";
$newline="<br/>";
$json   = '[
{
		"source":"symbols/2/2.png",
				"ypos":133,
				"template":"8B82CA47-41D2-D624-D6A2-37177CD82F28",
				"rotation":0,
				"type":"MyImage",
				"width":252,
				"depth":5,
				"height":159,
				"xpos":581
},
{
		"source":"symbols/2/2.png",
		"ypos":175,
		"template":"8B82CA47-41D2-D624-D6A2-37177CD82F28",
		"rotation":0,
		"type":"MyImage",
		"width":258,
		"depth":3,
		"height":163,
		"xpos":214
},
{
		"color":"0",
		"ypos":468.38,
		"fontSize":28,
		"xpos":156.95,
		"rotation":0,
		"type":"MyTextArea",
		"width":268.05,
		"depth":7,
		"height":244,
		"fontFamily":"Verdana Bold",
		"template":"8B82CA47-41D2-D624-D6A2-37177CD82F28"
}
]';

//$db = mongoConnect();
$db = mysqlConnect();
//$my_arr = json_decode($json, true);
//var_dump($my_arr);
//echo $newline;

/*foreach($my_arr as $i=>$ivalue){
		$sql = null;
		echo "<br/>???".$i."<br/>";
		foreach($my_arr[$i] as $key => $value){
	$sql[] = (is_numeric($value)) ? " $key= $value" : " $key = '" . mysqli_real_escape_string($db,$value) . "'"; 
		}

$sqlclause = implode(",",$sql);
echo "INSERT INTO `table` SET $sqlclause";
}
*/
/*mysql test select*/
$sth = mysqli_query($db,"SELECT * from test");
$rows = array();
while($r = mysqli_fetch_assoc($sth)) {
		    $rows[] = $r;
}
print json_encode($rows);

/**/
$videoID='7-7knsP2n5w';
//echo '<iframe width="560" height="315" src="//www.youtube.com/embed/'.$videoID.'" frameborder="0" allowfullscreen></iframe>';
//echo '<br/>execute #END#;<br/>';

?>
