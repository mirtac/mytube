<?php
session_start();
if(isSet($_SESSION["uid"])){
		$name = $_SESSION['name'];
}
else{
		$name = 'guest';
}
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" type="text/css" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js">
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="http://platform.twitter.com/widgets.js" async></script>

</script>
<script>
var start = 0;
var end = 0;
var db="mysql";
var obj;
//db = "mysql";
db = "mongo";
function clearDiv(){
		if(arguments.length>1){
				for (var i = 0; i < arguments.length; i++) {
						$(arguments[i]).html("");
				}

		}
		else if(arguments[0]=="all"){
				$("#video").html("");
				$("#videoPlay").html("");
				$("#relationVideo").html("");
				$("#commentDiv").html("");
		}
}
function httpGetRequest(method,data) {
		req = false;
		if(window.XMLHttpRequest) {
				try { req = new XMLHttpRequest();
				} catch(e) {
						req = false; }
		} else if(window.ActiveXObject) {
				try { req = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
						try { req = new ActiveXObject("Microsoft.XMLHTTP");
						} catch(e) { req = false; } 
				} 
		}
		if(req) {
				if(method=='addToList'){//history
						url = 'handle.php?type=addToList';
						which=data.which;
						url+="&which="+which+"&vid="+data.vid+"&title="+data.title;
						//req.onreadystatechange = processSearchReqChange;
						//console.log(url);return;
				}
				else if(method=='favorite'){
						url = 'handle.php?type=addToList';
						title=$("#"+data.vid+" .info .title").html();
						url+="&which=favorite"+"&vid="+data.vid+"&title="+title;
				}
				else if(method=='like'||method=='dislike'){//TODO
				}
				else {
						return false;
				}
	
				req.open("GET", url, true);
				req.send(null);
		}
		return true;
}
function getData(method,data) {
		req = false;
		if(window.XMLHttpRequest) {
				try { req = new XMLHttpRequest();
				} catch(e) {
						req = false; }
		} else if(window.ActiveXObject) {
				try { req = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
						try { req = new ActiveXObject("Microsoft.XMLHTTP");
						} catch(e) { req = false; } 
				} 
		}
		if(req) {
				if(method=='getlist'){//call by getData("getlist",listname.toString());
						url = 'handle.php';
						req.onreadystatechange = processListReqChange;
						$("#message").html(data+" List");
						parameter="?type=getlist&which="+data;
						clearDiv("all");
				}
				else if(method=='comment'){
						url = 'handle.php';
						req.onreadystatechange = processCommentReqChange;
						parameter="type=comment&vid="+data.vid;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
						}
				}
				else if(method=='test'){
						url = 'handle.php';
						req.onreadystatechange = processTestReqChange;
						parameter="type=comment&vid="+data.vid;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
						}
				}
				else if(method=='search'){
						url = 'search.php';
						req.onreadystatechange = processSearchReqChange;
						$("#message").html('searching...');
						parameter="search="+$("#search").val()+"&sortField="+$("#order").val()+"&method="+db;
						parameter+='&category='+$("#category");
						parameter+="&page="+data.page;
						clearDiv("all");
						start = new Date().getTime();
				}

	
				req.open("GET", url+parameter, true);
				req.send();
		}
		return true;
}

function getPostData(method,data) {
		//url = 'http://140.123.101.185:5182/~tan/data/youtube/search.php';
		//url = 'search.php';
		req = false;
		if(window.XMLHttpRequest) {
				try { req = new XMLHttpRequest();
				} catch(e) {
						req = false; }
		} else if(window.ActiveXObject) {
				try { req = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
						try { req = new ActiveXObject("Microsoft.XMLHTTP");
						} catch(e) { req = false; } 
				} 
		}
		if(req) {
				if(method=='search'){
						url = 'search.php';
						req.onreadystatechange = processSearchReqChange;
						$("#message").html('searching...');
						parameter="search="+$("#search").val()+"&sortField="+$("#order").val()+"&method="+db;
						parameter+='&category='+$("#category").val();
						clearDiv("all");
						console.log(parameter);
						start = new Date().getTime();
				}
				else if(method=='comment'){
						url = 'handle.php';
						req.onreadystatechange = processCommentReqChange;
						parameter="type=comment&vid="+data.vid;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
						}
				}
				else if(method=='test'){
						url = 'handle.php';
						req.onreadystatechange = processTestReqChange;
						parameter="type=comment&vid="+data.vid;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
						}
				}
	
				req.open("POST", url, true);
				req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				req.send(parameter);
		}
		return true;
}
function processListReqChange(){
		if(req.readyState==4){
				tmp = req.responseText;
				//$("#video").html(tmp);
				try {
						obj = JSON.parse(tmp);
				} 
				catch (e) {
						clearDiv("#message");
						console.log(tmp);
						return false;
				}
				parseJsonToList("",obj);
		}

}
function processSearchReqChange(){
		if(req.readyState==4){
				end = new Date().getTime();
				tmp = req.responseText;
				//$("#video").html(tmp);
				try {
						obj = JSON.parse(tmp);
				} 
				catch (e) {
						clearDiv("#message");
						console.log(tmp);
						return false;
				}
				parseJsonToList("total searching time : "+ (end - start) + " ms",obj);
		}
}
function processTestReqChange(){
		if(req.readyState==4){
				tmp = req.responseText;
				console.log(tmp);
		}
}
function processCommentReqChange(){
		if(req.readyState==4){
				commentHtmlCode='';
				tmp = req.responseText;
				try{
						obj = JSON.parse(tmp);
				}catch (e){
						console.log('fail on processCommentReqChange');
						console.log(tmp);
						return false;
				}
				for(var i = 0; i < obj.length; i++) {
						commentHtmlCode+='<div class="comment"><div class="date">'+obj[i].time; 
						commentHtmlCode+='</div><div class="name">'+obj[i].name+'</div>';
						commentHtmlCode+='<div class="content">'+obj[i].content+'</div></div>';

				}
				$("#commentDiv").html(commentHtmlCode);
				//console.log(commentHtmlCode);
		}
}
function parseJsonToList(message,obj){

		var innerstring='';
		if(message!=""){
				$("#message").html(message);
		}
		for(var i = 0; i < obj.length; i++) {
				/*date transfer*/
				if(obj[i].published){//should *1000,because wrong in put data to db
						time = new Date(obj[i].published.sec * 1000000);
						obj[i].published=time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
				}
				//obj[i].published=time;
				
				/**/
				innerstring+='<div class="videoList" id="'+obj[i].vid+'" onClick="playvideo(this)">';
				innerstring+='<img id="'+obj[i].vid+'"src="http://i.ytimg.com/vi/'+obj[i].vid;
				innerstring+='/mqdefault.jpg" onerror="imgError(this)"/>';
				innerstring+='<div class="info">';
				innerstring+='<div class="title">'+obj[i].title+'</div>';
				if(obj[i].published&&obj[i].author&&obj[i].duration&&obj[i].viewCount){
						innerstring+='<div class="published">'+obj[i].published+'</div>';
						innerstring+='<div class="author">'+obj[i].author;
						innerstring+='</div><div class="duration">'+obj[i].duration+'</div>';
						innerstring+='<div class="viewCount">'+obj[i].viewCount+'</div>';
				}
				innerstring+='</div>';
				innerstring+='<span class="jsondata">'+JSON.stringify(obj[i])+'</span></div>';
		}
		if(obj.length==0){
				innerstring='<div style="text-align:center"><h2>no result</h2></div>';
		}
		$("#video").html(innerstring);
		//console.log("###!"+$("#"+obj[0].vid+" .jsondata").text());

}
function playvideo(video){
		tmp = video.getElementsByClassName('jsondata')[0].innerHTML;
		videoJson=JSON.parse(tmp);
		$("#message").html('');
//		$("#message").css({"font-size":"1.1em","font-weight":"bolder"});
		htmlcode='';
		/*video*/
		iframeWidth=Math.floor(($(window).width()*6/10)-5);
		htmlcode='<iframe width="'+iframeWidth+'" height="'+Math.floor((iframeWidth*0.56));
		htmlcode+='" src="//www.youtube.com/embed/'+videoJson.vid;
		htmlcode+='" frameborder="0" allowfullscreen></iframe>';
		htmlcode+='<div class="title">'+videoJson.title+'</div>';
		htmlcode+='<div class="author">Author : '+videoJson.author+'</div><div class="infoRow">';
		htmlcode+='<div class="viewCount">views : '+videoJson.viewCount+'</div>';
		
//		htmlcode+='<div  class="favoriteCount">likes : '+videoJson.favoriteCount+'</div>';

		htmlcode+='<div id="likeSetting" class="btn-group favoriteCount">';
		htmlcode+='<a class="btn btn-info" id="likes" onclick="httpGetRequest(\'like\')">';
		htmlcode+='<img class="icon" src="image/like.png"/> ';
		htmlcode+=videoJson.favoriteCount+'</a>';
		htmlcode+='<a class="btn btn-info" id="dislikes" onclick="httpGetRequest(\'dislike\')">';
		if(!videoJson.dislike){
				videoJson.dislike=0;
		}
		htmlcode+='<img class="icon" src="image/dislike.png"> '+videoJson.dislike+'</a>';
		htmlcode+='<a onClick="video.vid=\''+videoJson.vid+'\';httpGetRequest(\'favorite\',video)" class="btn btn-info"><img class="icon" src="image/star.png"/></a>';
		htmlcode+='</div>';
	
		htmlcode+='</div><div class="content">';
		htmlcode+=videoJson.content+'</div><table class="infoTable"><tr><td>published</td><td>';
		htmlcode+=videoJson.published+'</td></tr><tr><td>category</td><td>'+videoJson.category+'</td></tr></table>';
		/*comment post form*/
		htmlcode+='<input type="text" id="comment" name="comment" placeholder="comment"></input>';
		htmlcode+='<input type="button" onClick="getPostData(\'comment\',\''+videoJson+'\')" value="comment"/>';
		if($("#relationVideo").html() == "" )$("#relationVideo").html($("#video").html());//TODO relationvideo list
		$("#video").html('');
		$("#videoPlay").html(htmlcode);
		
		//set history
		videoJson.which = "history";
		httpGetRequest("addToList",videoJson);

		getPostData('comment',videoJson);
		return;
}
function showList(listname){
		$("#userbtn").attr('class','btn-group');
		getData("getlist",listname);
		return true;
}
function imgError(image) {
		image.onerror = "";
		image.src = "http://i.ytimg.com/vi_webp/"+image.id+"/default.webp";
		return true;
}
function setUserInfo(){
		name = '<?php echo $name;?>';
		userSet='';
		if(name=='guest'){
				userSet=name+'    <a href="signin.php">SIGN IN?</a>';
				$("#userinfo").html(userSet);
		}
		else{
				//signin='  <a href="./handle.php?type=logout">logout</a>';
				//signin='<img class="userSetting" src="./image/gear.png" onmouseover="console.log(\'123\')">';
				$("#userName").html(name);
			

		}
		$("#usermenu").click(function(){
				if( $("#userbtn").attr('class')=='btn-group open' ){
				$("#userbtn").attr('class','btn-group');
				}
				else{
						$("#userbtn").attr('class','btn-group open');
				}
		});


}
</script>
</head>
<body>
<div class="topbar">
<span style="position:absolute;" id="message"></span>
<span style="position:absolute;right:5%;z-index:5" id="userinfo">
<div id="userbtn" class="btn-group">
<a class="btn btn-primary" href="#" id="userName"></a>
<a id="usermenu" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
<span class="caret"></span></a>
<ul class="dropdown-menu">
<li><a onClick="showList('history')">History List</a></li>
<li><a onClick="showList('upload')">Upload List</a></li>
<li><a onClick="showList('favorite')">Favorite List</a></li>
<li><a onClick="showList('like')">like List</a></li>
<li><a onClick="showList('comment')">comment List</a></li>
<li class="divider"></li>
<li><a href="./handle.php?type=logout">Logout</a></li>
</ul>
</div>
	

</span>
	<div class="searchBox">
		<form method="post" name="info">
		<input type="text" id="search" name="search" placeholder="search"></input>
		<input type="button" onClick="getPostData('search')" value="search"/>
		<!--img class="icon" src="image/gear.png" onClick="advSearch()"/-->
		<select name="orderBy" id="order"> 
		<option value="" selected="selected">--order by--</option>
		<option value="viewCount" >Views</option>
		<option VALUE="published">Upload date</option>
		<option VALUE="duration">Duration</option>
		</select>
		
		<select name="category" id="category"> 
		<option value="" selected="selected">--category--</option>
		<option value="Animals">Animals</option>
		<option value="Autos">Autos</option>
		<option value="Comedy">Comedy</option>
		<option value="Education">Education</option>
		<option value="Entertainment">Entertainment</option>
		<option value="Film">Film</option>
		<option value="Games">Games</option>
		<option value="Howto">Howto</option>
		<option value="Movies">Movies</option>
		<option value="Music">Music</option>
		<option value="News">News</option>
		<option value="Nonprofit">Nonprofit</option>
		<option value="People">People</option>
		<option value="Sports">Sports</option>
		<option value="Shows">Shows</option>
		<option value="Tech">Tech</option>
		<option value="Trailers">Trailers</option>
		<option value="Travel">Travel</option>
		</select>

		</form>
		</div>
</div>
<div class="pageContainer">
	<div class="video" id="video"><!-- use js to asign video  -->
	</div>
	<div class="videoPlay" id="videoPlay">
	<!-- for videoPlayer--!>
	</div>
	<div class="relationVideo" id="relationVideo"><!-- for relation list   --!></div>
	<div class="commentDiv" id="commentDiv"><!-- show comment under videoPlay--!>
	</div>
</div>
<script>
setUserInfo();
</script>
</body>




</html>
