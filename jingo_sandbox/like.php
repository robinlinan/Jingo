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

if($_GET['action']=="likemark"){
	$nid=$_POST['nid'];
	
	if(isset($_POST['like'])){
		mysql_query("insert into liked (nid,liker) values ('$nid','$uid')");
	}
	
	if(isset($_POST['dislike'])){
		mysql_query("delete from liked where nid=$nid and liker=$uid");
	}
	
	if(isset($_POST['mark'])){
		mysql_query("insert into bookmark (nid,uid) values ('$nid','$uid')");
	}
		
	
}

?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
		<link rel="stylesheet/less" href="style.less">
		<title>Jingo profile</title>
		<script src="less.js"></script>		
		
		<title>Jingo Note Rank</title>
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
		<h2>Note Rank</h2>
		<?php
		$liked=mysql_query("select nid,uname,posttime,text,hyperlink, count(*) as count from liked natural join note natural join user where authority='everybody' group by nid order by count desc");
		echo "<table border='1' style='margin-left:8px'>
		<tr>
		<th>note</th>
		<th>poster</th>
		<th>text</th>
		<th>hyperlink</th>
		<th>tag</th>
		<th>posttime</th>
		<th>popularity</th>
		<th>operation</th>
		</tr>";
		
		while($com=mysql_fetch_array($liked)){
			 echo"<form action='like.php?action=likemark' method='post'>
			 <tr>
			 <td> <input type='text' id='nid' name='nid' size='1' value= ".$com['nid']. " readonly='readonly' onclick='comment(".$com['nid'].")' /></td>
			 <td align='center'>".$com['uname']."</td>
			 <td align='center'>".$com['text']."</td>
			 <td align='center'>".$com['hyperlink']."</td>
			 <td align='center'>";
			 $nid=$com['nid'];
			 $note_tag = mysql_query("select tag from note_tag natural join tag where nid = $nid");
			 while($r_tag = mysql_fetch_array($note_tag)){
				echo "#".$r_tag['tag']."  ";
			 }
			 echo "</td>
			 <td align='center'>".$com['posttime']."</td>
			 <td align='center'>".$com['count']."</td>
			 
			 <td align='center'>";
			 $check=$com['nid'];
			 
			 $li=mysql_query("select checkLiked('$check','$uid')");
			 while($ra=mysql_fetch_array($li)){
				  if($ra[0]==0){
					  echo "<input type='submit' name='like' value='like' disabled='disabled' style ='color: #98C24D' />";
					  echo "<input type='submit' name='dislike' value='dislike'/>";

				  }else{
					  echo "<input type='submit' name='like' value='like' />";
					  echo "<input type='submit' name='dislike' value='dislike' disabled='disabled' style ='color: #98C24D'/>";
				  }
			 }
			 
			 $bm=mysql_query("select checkMarked('$check','$uid')");
			 while($rm=mysql_fetch_array($bm)){
				 if($rm[0]==0){
					 echo "<input type='submit' name='mark' value='mark' disabled='disabled' style ='color: #98C24D'/>";
				 }else{
					 echo "<input type='submit' name='mark' value='mark' />";
				 }
			 }
			 echo "</td>
			 	   </form>";
		}
		echo "</table>";
		?>
		
		
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