<?php
session_start();

if(isset($_GET['logout'])){	
	session_destroy();
	header("Location: index.php"); //Redirect the user
}

?>
<?php
if(!isset($_SESSION['name'])){
	$_SESSION['name']=$_SERVER['REMOTE_ADDR'];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat - Customer Module</title>
<link type="text/css" rel="stylesheet" href="style.css" />
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="js/utility.js"></script>
	<script type="text/javascript">
		// jQuery Document
		<?php
		if(empty($_GET["secretKey"]))
		{
			echo 'var name="'.$_SESSION['name'].'";';
			echo  'var hostName="'.$_GET["hostName"].'";';
			echo  'var type=1;';
		}
		else
		{
			echo 'var name="'.$_GET["userName"].'";';
			echo  'var hostName="'.$_GET["hostName"].'";';
			echo  'var type=2;';
		}
		?>
		var latestTime='';
		$(document).ready(function(){
			//If user submits the form
			$("#submitmsg").click(function(){
				var clientmsg = $("#usermsg").val();
				$.post("post.php", {text: clientmsg,type:type});
				$("#usermsg").val('');
				return false;
			});

			//Load the file containing the chat log
			function loadLog(){
				var oldscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
				$.ajax({
					url: "getMessage.php",
					cache: false,
					type:'POST',
					data:{userName:name,hostName:hostName,d:latestTime},
					success: function(data){
						if(data=="") return;
						var data=jQuery.parseJSON(data);
						for(i in data)
						{

							var h='<div class="msgln">'+data[i].dateTime+'<b>['+(data[i].type==1?data[i].userName:data[i].hostName)+'</b>]:'+data[i].message+'<br></div>';
							latestTime=data[i].dateTime;
							$("#chatbox").append(h);
							if(latestTime!="")
								playSound('notify');

						}

						var newscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
						console.log("old:"+oldscrollHeight+"new:"+newscrollHeight);
						if(newscrollHeight > oldscrollHeight){
							$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
						}
					}
				});
			}
			setInterval (loadLog, 1000);

			//If user wants to end session
			$("#exit").click(function(){
				var exit = confirm("Are you sure you want to end the session?");
				if(exit==true){window.location = 'index.php?logout=true';}
			});
		});
	</script>
</head>


<div id="wrapper">
	<div id="menu">
		<!--<p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>-->
		<div style="clear:both"></div>
	</div>	
	<div id="chatbox">

	</div>
	
	<form name="message" action="">
		<input name="usermsg" type="text" id="usermsg" size="63" />
		<input name="submitmsg" type="submit"  id="submitmsg" value="发送" />
	</form>
</div>

<?php

?>
<button onclick="playSound('notify');">Play</button>
<div id="sound"></div>
</body>
</html>