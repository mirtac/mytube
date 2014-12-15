<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js">
</script>
<script>
var start = 0;
var end = 0;
var method="mysql";
//method = "mysql";
				method = "mongo";
function change(){
		if(method=="mysql")method="mongo";
		else	method="mysql";
}
function getData() {
		//url = 'http://140.123.101.185:5182/~tan/data/youtube/search.php';
		url = './search.php';
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
				req.onreadystatechange = processReqChange;
				$("#message").html('searching...');
				req.open("POST", url, true);
				req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				start = new Date().getTime();
				req.send("search="+$("#search").val()+"&order="+$("#order").val()+"&method="+method);
				$("#videoPlay").html("");
				$("#relationVideo").html("");
		}
		return true;
}
function processReqChange(){
		if(req.readyState==4){
				end = new Date().getTime();
				tmp = req.responseText;
				//$("#video").html(tmp);
				console.log(tmp);
				try {
						obj = JSON.parse(tmp);
				} 
				catch (e) {
						$("#video").html("");
						//console.log(tmp);
						return false;
				}
//				console.log(obj);
				parseJsonToList();
		}
}
function parseJsonToList(){

		//TODO if method == mongo  ->    obj.published need to 
		/*
		//	getDate()	getFullYear()	getMonth()	;
		   var date = new Date(unix_timestamp*1000);
		// hours part from the timestamp
		var hours = date.getHours();
		// minutes part from the timestamp
		var minutes = "0" + date.getMinutes();
		// seconds part from the timestamp
		var seconds = "0" + date.getSeconds();

		// will display time in 10:30:23 format
		var formattedTime = hours + ':' + minutes.substr(minutes.length-2) + ':' + seconds.substr(seconds.length-2);
		*/

		var innerstring='';
		//innerstring = "total searching time : "+ (end - start) + " ms <br/>";
		$("#message").html("total searching time : "+ (end - start) + " ms");
		for(var i = 0; i < obj.length; i++) {
				tmp = obj[i];
				innerstring+='<div class="videoList" id="'+obj[i].id+'" onClick="playvideo(this)"><img id="'
				innerstring+=obj[i].id+'"src="http://i.ytimg.com/vi/'+obj[i].id;
				innerstring+='/mqdefault.jpg" onerror="imgError(this)"/><div class="info"><div class="title">';
				innerstring+=obj[i].title+'</div><div><span class="published">'+obj[i].published;
				innerstring+='</span><br/><span class="author">'+obj[i].author+'</span></div><span class="duration">';
				innerstring+=obj[i].duration+'</span><div class="viewCount">'+obj[i].viewCount
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
		iframeWidth=Math.floor(($(window).width()*6/10)-5);
		htmlcode='<iframe width="'+iframeWidth+'" height="'+Math.floor((iframeWidth*0.56));
		htmlcode+='" src="//www.youtube.com/embed/'+videoJson.id;
		htmlcode+='" frameborder="0" allowfullscreen></iframe>';//'<div class="title">'+videoJson.title+'</div>'
		htmlcode+='<div class="author">Author : '+videoJson.author+'</div><div class="infoRow">';
		htmlcode+='<div class="viewCount">views : '+videoJson.viewCount+'</div><div  class="favoriteCount">';
		htmlcode+='likes : '+videoJson.favoriteCount+'</div></div><div class="content">';
		htmlcode+=videoJson.content+'</div><table class="infoTable"><tr><td>published</td><td>';
		htmlcode+=videoJson.published+'</td></tr><tr><td>category</td><td>'+videoJson.category+'</td></tr></table>';
		if($("#relationVideo").html() == "" )$("#relationVideo").html($("#video").html());
		$("#video").html('');
		$("#videoPlay").html(htmlcode);
		return;
}
function imgError(image) {
		image.onerror = "";
		image.src = "http://i.ytimg.com/vi_webp/"+image.id+"/default.webp";
		return true;
}
</script>
</head>
<body>
<div class="topbar">
<span style="position:absolute;" id="message"></span>
	<div class="searchBox">
		<form method="post" name="info">
		<input type="text" id="search" name="search" placeholder="search"></input>
		<input type="button" onClick="getData()" value="search"/>
		order by:<select name="orderBy" id="order"> 
		<option value="" selected="selected">none</option>
		<option value="viewCount" >view count</option>
		<option VALUE="published"> creation time</option>
		<option VALUE="duration">video length</option>
		</select>
		</form>
		<button onclick="change()" value="switch"/>
	</div>
</div>
<div class="pageContainer">
	<div class="video" id="video"><!-- use js to asign video  -->
	</div>
	<div class="videoPlay" id="videoPlay">
	</div>
	<div class="relationVideo" id="relationVideo"></div>
</div>
</body>




</html>
