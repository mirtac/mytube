<?php
session_start();
if(isSet($_SESSION["uid"])){
		$name = $_SESSION['name'];
		$uid = $_SESSION['uid'];
}
else{
		$name = 'guest';
}
?>
<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/bootstrap.min.css" rel="stylesheet" media="screen">
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="./js/jquery.min.js">
<script type="text/javascript" src="./js/jquery.masonry.min.js">
<script src="./js/bootstrap.min.js"></script>
<script src="http://platform.twitter.com/widgets.js" async></script>

</script>
<script>
var start = 0;
var end = 0;
var db="mysql";
var obj;
var name;
var uid;
var count=0;
//db = "mysql";
db = "mongo";
function clearDiv(){
		if(arguments.length>=1){
				for (var i = 0; i < arguments.length; i++) {
						$(arguments[i]).html("");
				}

		}
		if(arguments[0]=="all"){
				$("#video").html("");
				$("#videoPlay").html("");
				$("#relationVideo").html("");
				$("#commentDiv").html("");
		}
}
function httpGetRequest(method,data) {
		var url='';
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
				else if(method=='deleteComment'){
						url = 'handle.php?type=deleteComment';
						url+="&cid="+data.cid+"&vid="+data.vid;
						req.onreadystatechange = processCommentReqChange;

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
		var url='';
		var parameter='';
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
						parameter="?type=comment&vid="+data.vid;
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
						parameter+='&category='+$("#c_category");
						parameter+="&page="+data.page;
						clearDiv("all");
						start = new Date().getTime();
				}
				else if(method=='getPage'){
						clearDiv('all');
						url = data.page;
						parameter='';
						req.onreadystatechange = function(){
								if(req.readyState==4){
										$("#video").html(req.responseText);
										if(data.vid){
												$('#operation option[value='+data.mod+']').attr('selected','selected');
												$('#vid').val(data.vid);
												getData('findByID');
										}

								}
						}
				}
				else if(method=='playVideo'){
						url='./handle.php';
						vid=data;
						parameter='?type=searchVideo&vid='+vid;
						req.onreadystatechange = playvideo;
				}
				else if(method=='findByID'){
						url='./handle.php';
						vid=$("#vid").val();
						parameter='?type=searchVideo&vid='+vid;
						req.onreadystatechange = function(){
								if(req.readyState==4){
										//$("#video").html();
										tmp=req.responseText;
										try {
												videoJson = JSON.parse(tmp);
										} 
										catch (e) {
												clearDiv("#message");
												console.log(tmp);
												return false;
										}
										if(tmp=='null'){
												alert("NOT IN DB");
												$("#title").val('');
												$("#content").val('');
												$("#category").val('');
												$("#duration").val('');
												$("#author").val('');
												$("#keyword").val('');


												return;
										}
										$("#title").val(videoJson.title);
										$("#content").val(videoJson.content);
										$("#category").val(videoJson.category);
										$("#duration").val(videoJson.duration);
										$("#author").val(videoJson.author);
										$("#keyword").val(videoJson.keyword);
								}
						};
				}
				else if(method=='like'||method=='dislike'){
						url = 'handle.php?type=videoLike';
						url+="&vid="+data+"&which="+method;
						req.onreadystatechange = function(){
								if(req.readyState==4){
										//TODO if fail:disable button;else update likecount;
										tmp=req.responseText;
										try {
												videoJson = JSON.parse(tmp);
												$("#likeNum").html(videoJson.favoriteCount);
												try{
														$("#dislikeNum").html(videoJson.dislike);
												}catch( ev){
														$("#dislikeNum").html('0');
												}
										} 
										catch (e) {
												console.log("like fail"+tmp);
												$("#"+method+"Num").attr('disabled',true);
												return false;
										}
								}
						}
				}
				else {
						console.log("getData wrong");
				}

				req.open("GET", url+parameter, true);
				req.send();
		}
		return true;
}

function getPostData(method,data) {
		//url = 'http://140.123.101.185:5182/~tan/data/youtube/search.php';
		//url = 'search.php';
		var url='';
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
						parameter+='&category='+$("#c_category").val();
						clearDiv("all");
						//console.log(parameter);
						start = new Date().getTime();
				}
				else if(method=='comment'){
						url = 'handle.php';
						req.onreadystatechange = processCommentReqChange;
						parameter="type=comment&vid="+data;
						if($("#comment").val() ==''){
						}
						else{
								parameter+="&message="+$("#comment").val();
								$("#comment").val('');
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
				var obj;
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
				var obj;
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
						commentHtmlCode+='<div class="comment">';
						commentHtmlCode+='<span class="name">'+obj[i].name+'</span>';
						commentHtmlCode+='<span class="date">'+obj[i].time+'</span>';
						if(uid==obj[i].uid){
								commentHtmlCode+='<button class="btn btn-warning" onclick="var datac=new Object();datac.cid=\''+obj[i].cid;
								commentHtmlCode+='\';datac.vid=\''+obj[i].vid+'\';httpGetRequest(\'deleteComment\',datac';
								commentHtmlCode+=')">'+'delete'+'</button>';
						}
						commentHtmlCode+='<div class="content">'+obj[i].content+'</div>';
						commentHtmlCode+='</div>';

				}
				$("#commentDiv").html(commentHtmlCode);
				//console.log(commentHtmlCode);
		}
}
function parseJsonToList(message,parseObj,where){
		var innerstring='';
		if(where){}
		else{
				where='#video';
		}
		if(message!=""){
				$("#message").html(message);
		}
		//for(var i = 0; i < parseObj.length; i++) {
		count=0;
		for(var i = 0; i < 20&&i<parseObj.length; i++,count++) {
				/*date transfer*/
				try{//should *1000,because wrong in put data to db
						time = new Date(parseObj[i].published.sec * 1000000);
						parseObj[i].published=time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
				}
				catch( e){
						parseObj[i].published = mongoIDToDate(parseObj[i]._id.$id);
				}
				//parseObj[i].published=time;	
				/**/
				innerstring+='<div class="videoList" id="'+parseObj[i].vid+'" >';
				innerstring+='<img id="'+parseObj[i].vid+'"src="http://i.ytimg.com/vi/'+parseObj[i].vid;
				innerstring+='/mqdefault.jpg" onerror="imgError(this)" '+
						'onClick="getData(\'playVideo\',\''+parseObj[i].vid+'\')"/>';
				innerstring+='<div class="info">';
				innerstring+='<div class="title">'+parseObj[i].title+'</div>';
				if(parseObj[i].published&&parseObj[i].author&&parseObj[i].duration&&parseObj[i].viewCount){
						innerstring+='<div class="published">'+parseObj[i].published+'</div>';
						innerstring+='<div class="author">'+parseObj[i].author;
						innerstring+='</div><div class="duration">'+parseObj[i].duration+'</div>';
						innerstring+='<div class="viewCount">'+parseObj[i].viewCount+'</div>';
				}
				innerstring+='</div>';
				if(parseObj[i].uid&&uid==parseObj[i].uid.$id&&$("#message").html()=='upload List'){
						innerstring+='<span class="listEditor">'+
							'<button class="btn-mini btn-info" '+
							'onclick="var setOpt=new Object();setOpt.vid=\''+parseObj[i].vid+
								'\';setOpt.page=\'./manage.html\';'+
								'setOpt.mod=\'update\';getData(\'getPage\',setOpt);">'+
								'<img class="icon" src="image/edit.png"/>'+'</button>'+
							'<button class="btn-mini btn-danger" '+
								'onclick="var setOpt=new Object();setOpt.vid=\''+parseObj[i].vid+
								'\';setOpt.page=\'./manage.html\';'+
								'setOpt.mod=\'delete\';getData(\'getPage\',setOpt);">'+
							'<img class="icon" src="image/delete.png"/>'+
							'</button></span>';
				//htmlcode+='<a class="btn btn-info" id="likes" onclick="getData(\'like\',\''+videoJson.vid+'\')">';
				}
				innerstring+='<span class="jsondata">'+JSON.stringify(parseObj[i])+'</span></div>';
		}
		if(parseObj.length==0){
				innerstring='<div style="text-align:center"><h2>no result</h2></div>';
				$(where).html(innerstring);
				return;
		}
		$(where).html(innerstring);
		$(window).off("scroll");
		$(window).scroll(function () {
				    if ($(document).scrollTop() + $(window).height() >= $(document).height()) {
				    		innerstring='';
				    		tmpCount=count;
				    if(parseObj.length<20)return;
					for(var i = count; i < (tmpCount+20)&&i<parseObj.length; i++,count++) {
					try{
					time = new Date(parseObj[i].published.sec * 1000000);
					parseObj[i].published=time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
					}
					catch( e){
					parseObj[i].published = mongoIDToDate(parseObj[i]._id.$id);
					}
					//parseObj[i].published=time;	
					/**/
					innerstring+='<div class="videoList" id="'+parseObj[i].vid+'" >';
					innerstring+='<img id="'+parseObj[i].vid+'"src="http://i.ytimg.com/vi/'+parseObj[i].vid;
					innerstring+='/mqdefault.jpg" onerror="imgError(this)" '+
					'onClick="getData(\'playVideo\',\''+parseObj[i].vid+'\')"/>';
					innerstring+='<div class="info">';
					innerstring+='<div class="title">'+parseObj[i].title+'</div>';
					if(parseObj[i].published&&parseObj[i].author&&parseObj[i].duration&&parseObj[i].viewCount){
					innerstring+='<div class="published">'+parseObj[i].published+'</div>';
					innerstring+='<div class="author">'+parseObj[i].author;
					innerstring+='</div><div class="duration">'+parseObj[i].duration+'</div>';
					innerstring+='<div class="viewCount">'+parseObj[i].viewCount+'</div>';
					}
					innerstring+='</div>';
					if(parseObj[i].uid&&uid==parseObj[i].uid.$id&&$("#message").html()=='upload List'){
							innerstring+='<span class="listEditor">'+
									'<button class="btn-mini btn-info" '+
									'onclick="var setOpt=new Object();setOpt.vid=\''+parseObj[i].vid+
									'\';setOpt.page=\'./manage.html\';'+
									'setOpt.mod=\'update\';getData(\'getPage\',setOpt);">'+
									'<img class="icon" src="image/edit.png"/>'+'</button>'+
									'<button class="btn-mini btn-danger" '+
									'onclick="var setOpt=new Object();setOpt.vid=\''+parseObj[i].vid+
									'\';setOpt.page=\'./manage.html\';'+
									'setOpt.mod=\'delete\';getData(\'getPage\',setOpt);">'+
									'<img class="icon" src="image/delete.png"/>'+
									'</button></span>';
							//htmlcode+='<a class="btn btn-info" id="likes" onclick="getData(\'like\',\''+videoJson.vid+'\')">';
					}
					innerstring+='<span class="jsondata">'+JSON.stringify(parseObj[i])+'</span></div>';
					}
					$("#video").html($("#video").html()+innerstring);
					}
		});
		//console.log("###!"+$("#"+parseObj[0].vid+" .jsondata").text());

}
function playvideo(){
		if(req.readyState==4){
				commentHtmlCode='';
				tmp = req.responseText;
				try{
						videoJson = JSON.parse(tmp);
				}catch (e){
						console.log('fail on processCommentReqChange');
						console.log(tmp);
						return false;
				}
				/*if($("#relationVideo").html() == "" )
						$("#relationVideo").html($("#video").html());//TODO relationvideo list*/
				var url='';
				reqC = false;
				if(window.XMLHttpRequest) {
						try { reqC = new XMLHttpRequest();
						} catch(e) {
								reqC = false; }
				} else if(window.ActiveXObject) {
						try { reqC = new ActiveXObject("Msxml2.XMLHTTP");
						} catch(e) {
								try { reqC = new ActiveXObject("Microsoft.XMLHTTP");
								} catch(e) { reqC = false; } 
						} 
				}
				if(reqC) {
						url = 'search.php';
						reqC.onreadystatechange = function(){
								if(reqC.readyState==4){
										console.log("in relationList parse");
										tmp = reqC.responseText;
										try{
												relavideo = JSON.parse(tmp);
										}catch (e){
												console.log('fail on playvideo.relationList');
												console.log(tmp);
												return false;
										}
										parseJsonToList("",relavideo,'#relationVideo');
								}
						};
						$("#message").html('searching...');
						parameter='?method=search&category='+videoJson.category;
						//console.log(parameter);
						reqC.open("GET", url+parameter, true);
						reqC.send();
				}


				obj.length=0;
				count=0;
				//tmp = video.getElementsByClassName('jsondata')[0].innerHTML;
//				$("#message").html('');
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
				htmlcode+='<a class="btn btn-info" id="likes" onclick="getData(\'like\',\''+videoJson.vid+'\')">';
				htmlcode+='<img class="icon" src="image/like.png"/> ';
				htmlcode+='<span id="likeNum">'+videoJson.favoriteCount+'</span></a>';
				htmlcode+='<a class="btn btn-info" id="dislikes" onclick="getData(\'dislike\',\''+videoJson.vid+'\')">';
				if(!videoJson.dislike){
						videoJson.dislike=0;
				}
				htmlcode+='<img class="icon" src="image/dislike.png"><span id="dislikeNum">'+videoJson.dislike+'</span></a>';
				htmlcode+='<a onClick="video.vid=\''+videoJson.vid+'\';httpGetRequest(\'favorite\',video)" class="btn btn-info"><img class="icon" src="image/star.png"/></a>';
				htmlcode+='</div>';

				htmlcode+='</div><div class="content">';
				
				if(videoJson.published){//should *1000,because wrong in put data to db
						time = new Date(videoJson.published.sec * 1000000);
						videoJson.published=time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
				}
				else{
						videoJson.published = mongoIDToDate(videoJson._id.$id);
				}

				htmlcode+=videoJson.content+'</div><table class="infoTable"><tr><td>published</td><td>';
				htmlcode+=videoJson.published+'</td></tr><tr><td>category</td><td>'+videoJson.category;
				htmlcode+='</td></tr></table></div>';
				/*comment post form*/
				htmlcode+='<div><input type="text" id="comment" name="comment" placeholder="comment"></input>';
				htmlcode+='<input type="button" onClick="getPostData(\'comment\',\''+videoJson.vid+'\')" value="comment"/>';
				$("#video").html('');
				$("#videoPlay").html(htmlcode);

				//set history
				videoJson.which = "history";
				httpGetRequest("addToList",videoJson);
				getPostData('comment',videoJson.vid);
		}
		return;
}
function showList(listname){
		try{
				obj.length=0;
		}catch(e){}
		count=0;
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
		uid = '<?php echo $uid;?>';

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
function mongoIDToDate(objID){//return YYYY/MM/DD
		time = new Date( parseInt(objID.substring(0,8) , 16) *1000 );
//		return time.toTimeString();
		return time.getFullYear()+"/"+time.getMonth()+"/"+time.getDate();
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
<li><a onClick="var setOpt=new Object();setOpt.page='./manage.html';getData('getPage',setOpt);$('#userbtn').attr('class','btn-group');">manage video</a></li>
<li><a onClick="showList('comment')">comment List</a></li>
<li class="divider"></li>
<li><a href="./handle.php?type=logout">Logout</a></li>
</ul>
</div>
	

</span>
	<div class="searchBox">
		<form method="post" name="info">
		<input type="text" id="search" name="search" placeholder="search"></input>
		<button type="button" class="btn_my" onClick="getPostData('search')" value="search">search</button>
		<!--img class="icon" src="image/gear.png" onClick="advSearch()"/-->
		<select name="orderBy" id="order"> 
		<option value="" selected="selected">--order by--</option>
		<option value="viewCount" >Views</option>
		<option VALUE="published">Upload date</option>
		<option VALUE="duration">Duration</option>
		<option VALUE="score">Relevance</option>
		</select>
		
		<select name="c_category" id="c_category"> 
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
	<div class="video" id="video"><!-- use js to asign video  --></div>
	<div class="relationVideo" id="relationVideo"><!-- for relation list   --!></div>
	<div class="videoPlay" id="videoPlay"><!-- for videoPlayer--!></div>
	<div class="commentDiv" id="commentDiv"><!-- show comment under videoPlay--!></div>
</div>
<script>
setUserInfo();
getPostData('search');
</script>
</body>




</html>
