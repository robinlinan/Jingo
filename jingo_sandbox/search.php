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

if(isset($_POST['Search'])){
	$un=$_POST['unameS'];
	if($un != null){
		$search= "SELECT distinct nid, uname, posttime, text, hyperlink FROM note NATURAL JOIN user WHERE text like '%$un%' and authority='everybody'";	
	}
}

if(isset($_GET['request_sent'])){
	$request_sent=$_GET['request_sent'];
    $requestSent="INSERT INTO friendrequest (uid,friend_id,senttime) VALUES('$uid','$request_sent','$time')";
	mysql_query($requestSent);
}

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
	<link rel="stylesheet/less" href="style.less">
	<title>Jingo friend</title>
	<script src="less.js"></script>
	
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
		<div class="wrapper" >
				<h1> Search Note </h1>
				<form action="search.php" method="post" >
				<p><strong> Search Note:</strong> <input type="text" name="unameS" />
				<input type="submit" value="Search" name="Search" style="margin-left:30px;font-size:16px"/><p>
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
		
				$receive_note=mysql_query($search);
				while($rec=mysql_fetch_array($receive_note)){
				echo"<form action='search.php?action=likemark' method='post'>
					<tr>
					<td><input type='text' id='nid' name='nid' size='1' value= ".$rec['nid']. " readonly='readonly' 
					onclick='comment(".$rec['nid'].")' /></td>";

		//			 <td>".'<a href="comment.php?nid='.$rec['nid'].'">'.$rec['nid']."</a>"."</td>";
		//			 <td><input type='text' name='nid' size='1' value=".$rec['nid']." /></td>
				echo"<td align='center'>".$rec['uname']."</td>
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
							  echo "<input type='submit' name='like' value='like' style='font-size:20px; color: #98C24D' disabled='disabled'/>";
						  }else{
							  echo "<input type='submit' name='like' value='like' style='font-size:20px' />";
						  }
					 }
			 
					 $bm=mysql_query("select checkMarked('$check','$uid')");
					 while($rm=mysql_fetch_array($bm)){
						 if($rm[0]==0){
							 echo "<input type='submit' name='mark' value='mark' style='font-size:20px; color: #98C24D' disabled='disabled' />";
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
				
				</div>
				
		</div>
		<script>
		
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