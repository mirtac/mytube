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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js">
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
				if(method=='addToList'){
						url = 'handle.php?type=addToList';
						which=data.which;
						url+="&which="+which+"&vid="+data.id+"&title="+data.title;
						//req.onreadystatechange = processSearchReqChange;
				}
				else if(method=='comment'){
				}
	
				req.open("GET", url, true);
				req.send(null);
		}
		return true;
}
function getData(method,id) {
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
						parameter="search="+$("#search").val()+"&order="+$("#order").val()+"&method="+db;
						clearDiv("all");
						start = new Date().getTime();
				}
				else if(method=='comment'){
						url = 'handle.php';
						req.onreadystatechange = processCommentReqChange;
						parameter="type=comment&vid="+id;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
						}
				}
				else if(method=='test'){
						url = 'handle.php';
						req.onreadystatechange = processTestReqChange;
						parameter="type=comment&vid="+id;
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
//				console.log(obj);
				parseJsonToList("total searching time : "+ (end - start) + " ms");
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
				console.log(commentHtmlCode);
		}
		else{
				console.log("["+req.readyState+"]");
				tmp = req.responseText;
				console.log(tmp);
		}
}
function parseJsonToList(message){

		var innerstring='';
		$("#message").html(message);
		for(var i = 0; i < obj.length; i++) {
				/*date transfer*/
				time = new Date(obj[i].published.sec * 1000000);//should *1000,because wrong in put data to db
				obj[i].published=time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
				//obj[i].published=time;
				
				/**/
				innerstring+='<div class="videoList" id="'+obj[i].id+'" onClick="playvideo(this)">';
				innerstring+='<img id="'+obj[i].id+'"src="http://i.ytimg.com/vi/'+obj[i].id;
				innerstring+='/mqdefault.jpg" onerror="imgError(this)"/>';
				innerstring+='<div class="info">';
				innerstring+='<div class="title">'+obj[i].title+'</div>';
				innerstring+='<div class="published">'+obj[i].published;
				innerstring+='</div><div class="author">'+obj[i].author+'</div><div class="duration">';
				innerstring+=obj[i].duration+'</div><div class="viewCount">'+obj[i].viewCount
				innerstring+='</div></div>';
				innerstring+='<span class="jsondata">'+JSON.stringify(obj[i])+'</span></div>';
		}
		if(obj.length==0){
				innerstring='<div style="text-align:center"><h2>no result</h2></div>';
		}
		$("#video").html(innerstring);
		//console.log("###!"+$("#"+obj[0].id+" .jsondata").text());

}
function playvideo(video){
		tmp = video.getElementsByClassName('jsondata')[0].innerHTML;
		videoJson=JSON.parse(tmp);
		$("#message").html(videoJson.title);
		$("#message").css({"font-size":"1.1em","font-weight":"bolder"});
		htmlcode='';
		/*video*/
		iframeWidth=Math.floor(($(window).width()*6/10)-5);
		htmlcode='<iframe width="'+iframeWidth+'" height="'+Math.floor((iframeWidth*0.56));
		htmlcode+='" src="//www.youtube.com/embed/'+videoJson.id;
		htmlcode+='" frameborder="0" allowfullscreen></iframe>';//'<div class="title">'+videoJson.title+'</div>'
		htmlcode+='<div class="author">Author : '+videoJson.author+'</div><div class="infoRow">';
		htmlcode+='<div class="viewCount">views : '+videoJson.viewCount+'</div><div  class="favoriteCount">';
		htmlcode+='likes : '+videoJson.favoriteCount+'</div></div><div class="content">';
		htmlcode+=videoJson.content+'</div><table class="infoTable"><tr><td>published</td><td>';
		htmlcode+=videoJson.published+'</td></tr><tr><td>category</td><td>'+videoJson.category+'</td></tr></table>';
		/*comment post form*/
		htmlcode+='<input type="text" id="comment" name="comment" placeholder="comment"></input>';
		htmlcode+='<input type="button" onClick="getData(\'comment\',\''+videoJson.id+'\')" value="comment"/>';
		if($("#relationVideo").html() == "" )$("#relationVideo").html($("#video").html());//TODO relationvideo list
		$("#video").html('');
		$("#videoPlay").html(htmlcode);
			
		videoJson.which = "history";
		httpGetRequest("addToList",videoJson);
		getData('comment',videoJson.id);
		return;
}
function imgError(image) {
		image.onerror = "";
		image.src = "http://i.ytimg.com/vi_webp/"+image.id+"/default.webp";
		return true;
}
function setUserInfo(){
		name = '<?php echo $name.'  <a href="./handle.php?type=logout">logout</a>';?>';
		signin='';
		if(name=='guest'){
				signin='    <a href="signin.php">SIGN IN?</a>';
		}
		else{

		}
		$("#userinfo").html(name+signin);


}
</script>
</head>
<body>
<div class="topbar">
<span style="position:absolute;" id="message"></span>
<span style="position:absolute;right:5%;z-index:5" id="userinfo"></span>
	<div class="searchBox">
		<form method="post" name="info">
		<input type="text" id="search" name="search" placeholder="search"></input>
		<input type="button" onClick="getData('search')" value="search"/>
		order by:<select name="orderBy" id="order"> 
		<option value="" selected="selected">none</option>
		<option value="viewCount" >view count</option>
		<option VALUE="published"> creation time</option>
		<option VALUE="duration">video length</option>
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
