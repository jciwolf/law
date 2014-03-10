
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat - Customer Module</title>
<link type="text/css" rel="stylesheet" href="style.css" />
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript">
$(document).ready(function(){
var latestTime="";
//Load the file containing the chat log
function loadLog(){
var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;

$.ajax({
url: "adminlist.php",
cache: false,
type:'POST',
data:{d:latestTime},
success: function(data){
if(data=="") return;
var data=jQuery.parseJSON(data);
for(i in data)
{
console.log(data[i]);
var h=$('<tr style="display: none;"><td>'+data[i].userName+'</td><td>'+(data[i].hostName)+'</td><td>'+data[i].message+'</td><td>'+data[i].dateTime+'</td><td><a href="http://115.28.50.119/chat/?secretKey=1&userName='+data[i].userName+'&hostName=cao" target="_blank">点击回复</a> </td></tr>');
latestTime=data[i].dateTime;
h.prependTo('#tb').show('slow');
}

}
});

}
	setInterval (loadLog, 1000);
});


</script>
</head>


<div id="wrapper">
	<table id="">
		<tr>
			<td>客户</td>
			<td>工作人员</td>
			<td>内容</td>
			<td>时间</td>
		</tr>
	</table>
	<table id="tb">

	</table>
</div>

</body>
</html>