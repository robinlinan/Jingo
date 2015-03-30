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

if($_GET['action'] == "delete_bookmark"){	
		$nid=$_POST['nid'];
		mysql_query("delete from bookmark where nid=$nid");
}

if($_GET['action'] == "delete_like"){	
		$nid2=$_POST['nid'];
		mysql_query("delete from liked where nid=$nid2 and liker=$uid");
}

?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
		<title>Jingo My Favourites</title>
		<link rel="stylesheet/less" href="style.less">
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
		
		<div class="right1">
		<h2>Bookmarked Notes</h2>
		<form action="bookmark.php?action=delete_bookmark" method="post">
		<?php
		$mark=mysql_query("select n.nid,uname,posttime,text,hyperlink from note n natural join user, bookmark b where n.nid=b.nid and b.uid=$uid");
		echo "<table border='1' style='margin-left:8px'>
		<tr>
		<th>note</th>
		<th>poster</th>
		<th>text</th>
		<th>hyperlink</th>
		<th>tag</th>
		<th>posttime</th>
		<th>operation</th>
		</tr>";
		
		while($com=mysql_fetch_array($mark)){
			echo"<tr>
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
			 <td align='center'><input type='submit' name='delete' value='delete'/></td>";
		}
		echo "</table>";

		?>
		
		</form>
	
		<div class="left">
		<h2>Liked Notes</h2>
		<form action="bookmark.php?action=delete_like" method="post">
		<?php
		
		$liked=mysql_query("select n.nid,uname,posttime,text,hyperlink from note n natural join user, liked l where n.nid=l.nid and l.liker=$uid;");
		echo "<table border='1' style='margin-left:8px'>
		<tr>
		<th>note</th>
		<th>poster</th>
		<th>text</th>
		<th>hyperlink</th>
		<th>tag</th>
		<th>posttime</th>
		<th>operation</th>
		</tr>";
		
		
		while($com2=mysql_fetch_array($liked)){
			echo"<tr>
			 <td> <input type='text' id='nid' name='nid' size='1' value= ".$com2['nid']. " readonly='readonly' onclick='comment(".$com2['nid'].")' /></td>
			 <td align='center'>".$com2['uname']."</td>
			 <td align='center'>".$com2['text']."</td>
			 <td align='center'>".$com2['hyperlink']."</td>
			 <td align='center'>";
			 $nid2=$com2['nid'];
			 $note_tag2 = mysql_query("select tag from note_tag natural join tag where nid = $nid2");
			 while($r_tag2 = mysql_fetch_array($note_tag2)){
				echo "#".$r_tag2['tag']."  ";
			 }
			 echo "</td>
			 <td align='center'>".$com2['posttime']."</td>
			 <td align='center'><input type='submit' name='delete' value='delete'/></td>";
		}
		echo "</table>";
		
		?>
		</form>
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