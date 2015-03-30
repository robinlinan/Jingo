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

// Logout;

	//creat a filter

if($_GET['action'] == "create"){
	if($_POST['state_select']=="Create State"){
		$state=$_POST['state_create'];
	}else{
		$state=$_POST['state_select'];
	}
	
	if($_POST['sch_date']=="Select A Day"){
		$s_date=$_POST['sch_time'];
	}else{
		$s_date=$_POST['sch_date'];
	}
	
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$a=$_POST['area'];
	$loc=$_POST['loc_select'];
	$str=explode(",",$loc);
	$lat=$str[0];
	$lng=$str[1];
	$r=$_POST['radius'];
	
	$tag=$_POST['tag'];
	$filter_id=mysql_query("select getFID('$uid','$state','$s_date','$starttime','$endtime','$a','$lng','$lat','$r')");
	while($fil=mysql_fetch_array($filter_id)){
		foreach ($tag as $tag_key){
			$tid=mysql_query("select getTID('$tag_key')");
			while($row=mysql_fetch_array($tid)){
				mysql_query("insert into filters_tag (fid,tid) values ('$fil[0]','$row[0]')");  
			}
						
		}
	}	

}
	
//delete a filter
if($_GET['action'] == "delete"){
	
	//$fid 如何获取
	
	if(isset($_POST['fid'])){

		$fid=$_POST['fid'];
		$filter_tag_delete = "delete from filters_tag where fid = $fid";
		$filter_delete = "delete from filters where fid = $fid";//按照fid删除filter
		mysql_query($filter_tag_delete);
		mysql_query($filter_delete);
	}  
}	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Jingo Filter</title>
		
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
		<h2> Create Filter </h2>
		
		<form action='filters.php?action=create' method='post' name='form1'>
			<!--<strong>state: </strong><input type="text" name="state"/>-->
			
			<p>	<strong>state: </strong>
			<select name="state_select" onchange="shows()">
			<?php
				$state_show=mysql_query("select state from state");
				while($state_row=mysql_fetch_array($state_show)){
					echo "<option>".$state_row['state']."</option>";
				}
			?>
			<option>Create State</option>
			</select>
			<input type="text" name="state_create" id="state_create" style="display:none" size="21"/>
			
			<strong>tag:</strong>
			<?php
				include('conn.php');
				$uid=$_SESSION['uid'];
//				$sql = "SELECT fid, state, tag, sch_date, starttime, endtime, longitude, latitude FROM filters_view WHERE uid = '";
//				$sql = $sql.$_SESSION['uid']."'";
				
//				$user_filters = mysql_query($sql);
		 
				$sql_tag = "SELECT tag FROM tag";
				$tag_query = mysql_query($sql_tag);
//when style conflicts, use !important to define priority		
				while($result_1 = mysql_fetch_array($tag_query)){
					echo"<input type='checkbox' style='width:20px; font-size:16px' name='tag[]' value='".$result_1['tag']."'/>".$result_1['tag'];
				}
					
			?>
			</p>
			
			<p>
			<!--下拉式菜单显示state-->
			<strong>schedule:</strong>
			<!--<input type = "select" name = "sch_date"> -->
			<select name='sch_date' onchange="show()">
				<option value = "everyday">everyday</option>
				<option value = "Mondays">Mondays</option>
				<option value = "Tuesdays">Tuesdays</option>
				<option value = "Wednesdays">Wednesdays</option>
				<option value = "Thursdays">Thursdays</option>
				<option value = "Fridays">Fridays</option>
				<option value = "Saturdays">Saturdays</option>
				<option value = "Sundays">Sundays</option>
				<option value = "Sundays">weekday</option>
				<option value = "weekend">weekend</option>
				<option value = "Select A Day">Specified Day</option>
			</select>
			<input type="text" name="sch_time" id="sch_time" style="display:none" size="21"/>
			
			<strong>starttime:</strong> <input type="text" name="starttime" id="starttime"/>
			<strong>endtime: </strong><input type="text" name="endtime" id="endtime"/>
			</p>
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
			
			<input type="submit" value = "create"/>
			</p>
		</form>
		
		
		<?php
		
		 include('conn.php');
		 $uid=$_SESSION['uid'];
		 $sql = "SELECT fid, state, sch_date, starttime, endtime, area, longitude, latitude, radius FROM filters_view WHERE uid = $uid GROUP BY fid";
		 $user_filters = mysql_query($sql);
						
			// your filters
			echo "<h2>Your Filters</h2>
				  <div style='position:absolute; height:300px; overflow:auto'>
				  <table border='1' style='margin-left:8px'>
				  <tr>
				  <th>filter</th>
				  <th>state</td>
				  <th>tag</td>
				  <th>sch_date</th>
				  <th>starttime</th>
				  <th>endtime</th>
				  <th>area</th>
				  <th>longitude</th>
				  <th>latitude</th>
				  <th>radius</th>
				  <th>operation</th>
				  </tr>";
		 
		 while($result = mysql_fetch_array($user_filters)){
			
			echo"<form action='filters.php?action=delete' method='post'>
				 <tr>
			
			     <td><input type='text' name='fid' size='1' value=".$result['fid']." readonly='readonly' /></td>
			     <td align='center'>".$result['state']."</td>
			
				 <td align='center'>";
				 $fid_tag = $result['fid'];
				 $filter_tag = mysql_query("select tag from filters_tag natural join tag where fid = $fid_tag");
				 while($r_tag = mysql_fetch_array($filter_tag)){
					echo "#".$r_tag['tag']."  ";
				 }			 
				 
				 echo "</td>
			     <td align='center'>".$result['sch_date']."</td>
				 <td align='center'>".$result['starttime']."</td>
				 <td align='center'>".$result['endtime']."</td>
				 <td align='center'>".$result['area']."</td>
				 <td align='center'>".$result['longitude']."</td>
				 <td align='center'>".$result['latitude']."</td>
				 <td align='center'>".$result['radius']."</td>
				 <td><input type='submit' name='delete' value='delete'/></td>
				 </tr>
				 </form>";
		}
			echo "</table>";
			echo "</div>";
			mysql_free_result($user_filters);
			mysql_close();
		?>
		</div>
		
		<script type="text/javascript">
			function shows(){
				var leng=document.form1.state_select.length;
				leng=leng-1;
				var x=document.form1.state_select.options[leng].selected;
				if(x==true){
					state_create.style.display="";
				}else{
					state_create.style.display="none";
					document.getElementById("state_create").value="";
				}
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
			});
			
			function openMyWindow() { 
	            ret = window.showModalDialog("googlemaps.html",window, 'dialogWidth=1000px;dialogHeight=600px'); 
	            if (ret != null) 
	            { 
	              window.document.getElementById("loc_select").value = ret; 
	               
	            } 
	        }   
				

			function comment(nid){
				window.location.href="comment.php?nid="+nid;
			}
		

		</script>
		<footer>
			<div class="wrapper">
				Jingo - A place to share
			</div>
		</footer>
		
	</body>
</html>