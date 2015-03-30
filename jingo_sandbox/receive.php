<?php
session_start();
ini_set('session.gc_maxlifetime',300);
if($_GET['action'] == "logout"){
    unset($_SESSION['uid']);
    unset($_SESSION['uname']);
    unset($_SESSION['state']);
    unset($_SESSION['curtime']);
    echo 'logout sucess <a href="login.html">login</a>';
    exit;
}

include('conn.php');
$uid=$_SESSION['uid'];

date_default_timezone_set('America/New_York');
$time = date('Y-m-d H:i:s');

if($_GET['action'] == "receive"){
	$st= $_POST['state'];
	if($_POST['curtime']=="select time"){
		$t=$_POST['time_select'];
	}else{
		$t=$_POST['curtime'];
	}
	$a=$_POST['area'];
	$loc=$_POST['loc_select'];
	$str=explode(",",$loc);
	$lat=$str[0];
	$lng=$str[1];

	
	$_SESSION['state']=$st;
	$_SESSION['curtime']=$t;
	mysql_query("call jingodb.receive_insert('$uid','$st','$t','$a','$lng','$lat');");


}else{
	$st=$_SESSION['state'];
	$t=$_SESSION['curtime'];
}


$sql="SELECT distinct nid, poster, posttime, text, hyperlink FROM received WHERE uid=$uid and state='$st'  and curtime='$t'";	

if($_GET['action']=="likemark"){
	$nid=$_POST['nid'];
	
	if(isset($_POST['like'])){
		mysql_query("insert into liked (nid,liker) values ('$nid','$uid')");
	}
	
	if(isset($_POST['mark'])){
		mysql_query("insert into bookmark (nid,uid) values ('$nid','$uid')");
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Jingo Recieve Note</title>
		
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
		<link rel="stylesheet/less" href="style.less">
		<title>Jingo profile</title>
		<script src="less.js"></script>
		
		<!-datetime picker->
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
		<style type="text/css">
		.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; } 
		.ui-timepicker-div dl { text-align: left; } 
		.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px;} 
		.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; } 
		.ui-timepicker-div td { font-size: 90%; } 
		.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; } 
		.ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label, 
		.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px} 
		</style>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		
				
	</head>
	<body>
		<header>
			<div class="wrapper">
				<img src="gfx/logo.png">
				<?php
				if($_SESSION['uid']!=null){ ?>
					<a href="profile.php" class="navigator">Profile | </a>
					<a href="friend.php" class="navigator">Friend | </a>
					<a href="note.php" class="navigator">Post note |</a>
					<a href="search.php" class="navigator">Search note |</a>
					<a href="filters.php" class="navigator">Filters | </a>
					<a href="receive.php" class="navigator">Receive | </a>
					<a href="like.php" class="navigator">Note Rank | </a>
					<a href="bookmark.php" class="navigator">My favorites | </a>
					
				<?php echo "<a href='login.php?action=logout' id='logout'>logout [{$_SESSION['uname']}]</a>";
				} else {
					echo "<a href='login.html'><id='name'>login</p></a>";
				}
				?>
			</div>
		</header>
		
		<div class="right1">
		<h2>Receiving Note</h2>
		<form action='receive.php?action=receive' method='post' name='form1'>
		<p>
		<strong>state:</strong><select name='state'>
		<?php
		$getstate=mysql_query("SELECT state FROM state");
		while($state=mysql_fetch_array($getstate)){
			echo "<option>".$state['state']."</option>";
		}
		?>
		</select>	
		
		<strong>time:</strong> <select name="curtime" onchange="show()">
			<option value="<?php echo $time?>">current time</option>
			<option>select time</option>
		</select>
		<input type="text" name="time_select" id="time_select" style="display:none" size="21"/>
		
		<strong>area:</strong><select name='area'>
		<option></option>
		<?php
		$getarea=mysql_query("SELECT area FROM area");
		while($area=mysql_fetch_array($getarea)){
			echo "<option>".$area['area']."</option>";
		}
		?>
		</select>	
		<strong>location:</strong><input type="text" name="loc_select" id="loc_select" onclick="javascript:openMyWindow();" size="27"/>
		<input type="submit" name="receive" value="receive"/>
		</select>
		</p>
		</form>

		
		<h2>Note Received</h2>
				
		<?php
		echo "<div style='position:absolute; height:300px; overflow:auto'>
		<table border='1' style='margin-left:8px'>
		<tr>
		<th>note</th>
		<th>poster</th>
		<th>text</th>
		<th>hyperlink</th>
		<th>tag</th>
		<th>posttime</th>
		<th>operation</th>
		</tr>";
		
		$receive_note=mysql_query($sql);
		while($rec=mysql_fetch_array($receive_note)){
		echo"<form action='receive.php?action=likemark' method='post'>
			<tr>
			<td><input type='text' id='nid' name='nid' size='1' value= ".$rec['nid']. " readonly='readonly' 
			onclick='comment(".$rec['nid'].")' /></td>";

//			 <td>".'<a href="comment.php?nid='.$rec['nid'].'">'.$rec['nid']."</a>"."</td>";
//			 <td><input type='text' name='nid' size='1' value=".$rec['nid']." /></td>
		echo"<td align='center'>".$rec['poster']."</td>
			 <td align='center'>".$rec['text']."</td>
			 <td align='center'>".$rec['hyperlink']."</td>
			 <td align='center'>";
			 $nid_tag = $rec['nid'];
			 $note_tag = mysql_query("select tag from note_tag natural join tag where nid = $nid_tag");
			 while($r_tag = mysql_fetch_array($note_tag)){
				echo "#".$r_tag['tag']."  ";
			 }
			 echo "</td>
			 <td>".$rec['posttime']."</td>
			 <td align='center'>";
			 $check=$rec['nid'];
			 
			 $li=mysql_query("select checkLiked('$check','$uid')");
			 while($ra=mysql_fetch_array($li)){
				  if($ra[0]==0){
					  echo "<input type='submit' name='like' value='like' style='font-size:20px; color: #98C24D' disabled='disabled'  style ='color: #98C24D'/>";
				  }else{
					  echo "<input type='submit' name='like' value='like' style='font-size:20px' />";
				  }
			 }
			 
			 $bm=mysql_query("select checkMarked('$check','$uid')");
			 while($rm=mysql_fetch_array($bm)){
				 if($rm[0]==0){
					 echo "<input type='submit' name='mark' value='mark' style='font-size:20px; color: #98C24D' disabled='disabled'/>";
				 }else{
					 echo "<input type='submit' name='mark' value='mark' style='font-size:20px' />";
				 }
			 }
			
			 echo "</td>
			 	   </form>";
		}
		echo "</table>";
		echo "</div>";
		?>
		
		</form>
		</div>
		
		
		<script>
		
		function comment(nid){
			window.location.href="comment.php?nid="+nid;
		}
		
		
		//datetime picker 
		$(function(){
			$('#time_select').datetimepicker({
				showSecond:true,
				timeFormat: 'hh:mm:ss'
			});
		});
				
		function show(){
			var leng=document.form1.curtime.length;
			leng=leng-1;
			var x=document.form1.curtime.options[leng].selected;
			if(x==true){
				time_select.style.display="";
			}else{
				time_select.style.display="none";
				document.getElementById("time_select").value="";
			}
		}
/*		
		function showloc(){
			var leng=document.form1.curlocation.length;
			leng=leng-1;
			var y=document.form1.curlocation.options[leng].selected;
			if(y==true){
				loc_select.style.display="";
			}else{
				loc_select.style.display="none";
				document.getElementById("loc_select").value="";
			}	
		}
*/		
		function openMyWindow() { 
            ret = window.showModalDialog("googlemaps.html",window, 'dialogWidth=1000px;dialogHeight=600px'); 
            if (ret != null) 
            { 
              window.document.getElementById("loc_select").value = ret; 
               
            } 
        } 
        
        

		
		</script> 
		<footer>
			<div class="wrapper">
				Jingo - A place to share
			</div>
		</footer>
		x
	</body>
</html>