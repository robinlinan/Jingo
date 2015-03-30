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

//delete a note
if($_GET['action'] == "delete"){	

		$nid=$_POST['nid'];
		mysql_query("delete from note_tag where nid = $nid");
		mysql_query("delete from comment where nid=$nid");
		mysql_query("delete from liked where nid=$nid");
		mysql_query("delete from bookmark where nid=$nid");
		mysql_query("delete from note where nid = $nid");
}

if($_GET['action'] == "create"){
	
	if($_POST['sch_date']=="Select A Day"){
		$s_date=$_POST['sch_time'];
	}else{
		$s_date=$_POST['sch_date'];
	}
	$text=$_POST['text'];
	$hyper=$_POST['hyperlink'];
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$a=$_POST['area'];
	$loc=$_POST['loc_select'];
	$str=explode(",",$loc);
	$lat=$str[0];
	$lng=$str[1];
	$r=$_POST['radius'];
	$authority=$_POST['authority'];
	
	$tag_select=$_POST['jquery-tagbox-select'];
	$tag_text=$_POST['jquery-tagbox-text'];
	if($tag_select!=null and $tag_text!=null){
		$alltag=$tag_select.",".$tag_text;	
	}elseif($tag_select==null and $tag_text!=null){
		$alltag=$tag_text;
	}elseif($tag_select!=null and $tag_text==null){
		$alltag=$tag_select;
	}else{
		$alltag="shopping";
	}
	$tags=explode(",", $alltag);
	
	mysql_query("insert into note (nid,tid) values ('$n[0]','$row[0]')");  
	
	
	$note_id=mysql_query("select getNID('$uid','$text','$hyper','$s_date','$starttime','$endtime','$a','$lng','$lat','$r','$authority')");
	while($n=mysql_fetch_array($note_id)){
		foreach ($tags as $tag_key){
		$tid=mysql_query("select getTID('$tag_key')");
			while($row=mysql_fetch_array($tid)){
				mysql_query("insert into note_tag (nid,tid) values ('$n[0]','$row[0]')");  
			}
		}
	}
	
}		
	
?>

<!DOCTYPE html>
<html lang="en-IN">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet/less" href="style.less">
		<title>Jingo note</title>
		<script src="less.js"></script>
		
		<!-datetime picker->
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		
		<!-add tag->
		<link rel="stylesheet" href="http://jquery-tagbox.googlecode.com/hg/css/jquery.tagbox.css" />
		<script type="text/javascript" src="http://jquery-tagbox.googlecode.com/hg/js/jquery.tagbox.js"></script>
		
		<style type="text/css">
		.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; } 
		.ui-timepicker-div dl { text-align: left; } 
		.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px;} 
		.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; } 
		.ui-timepicker-div td { font-size: 90%; } 
		.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; } 
		.ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label, 
		.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px} 
		
		div.row {
			padding: 2px;
			font-family: Verdana, sans-serif;

		}
	  
		div.row label {
	    	font-weight: bold;
	    	display: block;
	    	padding: 0px 0px 10px;
	    }

		</style>

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
		<div class="rightside">		
		<form action="note.php?action=create" method="post" name="form1">
			<?php
			include('conn.php');
			$uid=$_SESSION['uid'];
			$sql_tag = "SELECT tag FROM tag";
			$tag_query = mysql_query($sql_tag);
			echo" 
			<div class='row'>
			<label for='jquery-tagbox-text'>tags:</label>
			<input type='text' id='jquery-tagbox-text' name='jquery-tagbox-text'/>
			</div>
			<div class='row'>
			<select name='' id='jquery-tagbox-select-options'>";
			while($result_1 = mysql_fetch_array($tag_query)){
			echo"
			<option name = '' value='".$result_1['tag']."'>".$result_1['tag']."</option>";
			}
			echo"
			</select>
			<input type='text' id='jquery-tagbox-select' name='jquery-tagbox-select'/>
			"
			?>
		</div>
					
		</div>

		
		<h4>Post Note </h4>
			<!-- <input type="text" name="text"/>  -->
			<p><textarea cols="50" rows="8" name="text"></textarea></p>
			<p><strong>hyperlink:</strong>
			<input type="text" name="hyperlink" />
			<strong>schedule:</strong>
			<!--<input type = "select" name = "sch_date">-->
			<select name='sch_date' onchange="show()">
				<option value = "everyday">everyday</option>
				<option value = "Mondays">Mondays</option>
				<option value = "Tuesdays">Tuesdays</option>
				<option value = "Wednesdays">Wednesdays</option>
				<option value = "Thursdays">Thursdays</option>
				<option value = "Fridays">Fridays</option>
				<option value = "Saturdays">Saturdays</option>
				<option value = "Sundays">Sundays</option>
				<option value = "weekday">weekday</option>
				<option value = "weekend">weekend</option>
				<option value = "Select A Day">Specified Day</option>
			</select>
			<input type="text" name="sch_time" id="sch_time" style="display:none" size="21"/>
			<strong>starttime:</strong> <input type="text" name="starttime" id="starttime"/>
			<strong>endtime: </strong><input type="text" name="endtime" id="endtime"/>
			<p>
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
			<strong>radius:</strong><input type="text" name="radius" />
			<strong>authority:</strong>
			<select name='authority'>
				<option value = "everybody">everyone</option>
				<option value = "friend">friend</option>
				<option value = "myself">myself</option>
			<input type="submit" value = "create"/>
			</p>
		</form>

		<?php
		 include('conn.php');
		 $uid=$_SESSION['uid'];
		 $uname = $_SESSION['uname'];
		 //$sql = "SELECT nid, poster, text, tag, hyperlink, posttime, longitude, latitude, radius, sch_date, starttime, endtime, authority FROM view_note WHERE poster = $uname GROUP BY nid";
		 $sql = "SELECT * FROM view_note WHERE poster = '$uname' GROUP BY nid";
		 $user_note = mysql_query($sql);
			// your note
			echo "<h4>Your note</h4>
				  <div style='position:absolute; height:220px; overflow:auto'>
				  <table border='1' style='margin-left:8px'>
				  <tr>
				  <th>note</th>
				  <th>text</th>
				  <th>hyperlink</th>
				  <th>tag</th>
				  <th>posttime</th>
				  <th>sch_date</th>
				  <th>starttime</th>
				  <th>endtime</th>
				  <th>area</th>
				  <th>longitude</th>
				  <th>latitude</th>
				  <th>radius</th>
				  <th>authority</th>
				  <th>operation</th>
				  </tr>";
		 
		 while($result = mysql_fetch_array($user_note)){
			echo"<form action='note.php?action=delete' method='post'>
				 <tr>
				 <td><input type='text' id='nid' name='nid' size='1' value= ".$result['nid']. " readonly='readonly' 
				 onclick='comment(".$result['nid'].")' /></td>";

	//			 <td>".'<a href="comment.php?nid='.$result['nid'].'">'.$result['nid']."</a>"."</td>";
			    echo "<td align='center'>".$result['text']."</td>
				 <td align='center'>".$result['hyperlink']."</td>
				 <td align='center'>";
				 $nid_tag = $result['nid'];
				 $note_tag = mysql_query("select tag from note_tag natural join tag where nid = $nid_tag");
				 while($r_tag = mysql_fetch_array($note_tag)){
					echo "#".$r_tag['tag']."  ";
				 }
				 echo "</td>
				 
			     <td align='center'>".$result['posttime']."</td>
				 <td align='center'>".$result['sch_date']."</td>
				 <td align='center'>".$result['starttime']."</td>
				 <td align='center'>".$result['endtime']."</td>
				 <td align='center'>".$result['area']."</td>
				 <td align='center'>".$result['longitude']."</td>
				 <td align='center'>".$result['latitude']."</td>
				 <td align='center'>".$result['radius']."</td>
				 <td align='center'>".$result['authority']."</td>
				 <td align='center'><input type='submit' name='delete' value='delete'/></td>
				 </tr>
				 </form>";
		}
		echo "</table>";
		echo "</div>";

			mysql_close();
		?>
		</div>
		
		<script>	
		function comment(nid){
			window.location.href="comment.php?nid="+nid;
		}
			
		function show(){
			var leng=document.form1.sch_date.length;
			leng=leng-1;
			var x=document.form1.sch_date.options[leng].selected;
			if(x==true){
				sch_time.style.display="";
			}else{
				sch_time.style.display="none";
				document.getElementById("sch_time").value="";
			}
		}
			
		$(function(){
			$('#sch_time').datepicker({

			});
			$('#starttime').timepicker({
				showSecond:true,
				timeFormat: 'hh:mm:ss'
			});
			$('#endtime').timepicker({
				showSecond:true,
				timeFormat: 'hh:mm:ss'
			});
			
			$("#jquery-tagbox-text").tagBox();
		    $("#jquery-tagbox-select").tagBox({ 
		        enableDropdown: true, 
		        dropdownSource: function() {
		        	return jQuery("#jquery-tagbox-select-options");
		        }
		      });

		     });
		
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
	</body>
</html>