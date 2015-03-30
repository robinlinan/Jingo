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

if(isset($_POST['Confirmed'])){
    $n=$_POST['requester'];
   
   $response = "UPDATE friendrequest SET responsetime='$time', status='Confirmed' where uid=getUID('$n') and friend_id=$uid and status is null";
   mysql_query($response);

}

if(isset($_POST['Declined'])){
	$n=$_POST['requester'];
    
    $response = "UPDATE friendrequest SET responsetime='$time', status='Declined' where uid=getUID('$n') and friend_id=$uid and status is null";
	mysql_query($response);
}

if(isset($_POST['Search'])){
	$un=$_POST['unameS'];
	if($un != null){
		$search= "SELECT uid, uname FROM user WHERE uname like '%$un%' and uid !=$uid and (uid not in (select u.uid from friendrequest f, user u where f.friend_id=u.uid and f.uid=$uid and status='Confirmed')) and (uid not in (select uid from friendrequest natural join user where friend_id=$uid and status='Confirmed'))";
	}
}

if(isset($_GET['request_sent'])){
	$request_sent=$_GET['request_sent'];
    $requestSent="INSERT INTO friendrequest (uid,friend_id,senttime) VALUES('$uid','$request_sent','$time')";
	mysql_query($requestSent);
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
				<h1>Friend Request</h1>
				<p >
				<?php
				$friend_request = mysql_query("select * from user natural join friendrequest where friend_id=$uid and status is NULL");
				echo "<table border='1' style='margin-left:30px'>
				<tr>
				<th>requester</th>
				<th>request_time</th>
				<th>response</th>
				</tr>";

		        while($row = mysql_fetch_array($friend_request))
		        {
		        	echo
		        		 "<form action='friend.php?' method='POST'> 
		        		  <tr>
    
		        		  <td align='center'><input type='text' name='requester' value=".$row['uname']." readonly='readonly' /></td>
		        		  <td align='center'><input type='text' name='senttime' value=".$row['senttime']." readonly='readonly' /></td>
		        	      <td> 
		        	      <input type='submit' name='Confirmed' value='Confirmed' /> 
		        	      <input type='submit' name='Declined' value='Declined' /> 
		        	      </td>
		        	      </tr>
		        	      </form>";
		        }
       
		        echo "</table>"; 
				?>		
				</p>
		
				<h1> Search Friend </h1>
				<form action="friend.php" method="post" >
				<p><strong>user name:</strong> <input type="text" name="unameS" />
				<input type="submit" value="Search" name="Search" style="margin-left:30px;font-size:16px"/><p>
				</form>
				<?php
				$friend = mysql_query($search);
				echo "<table border='1' style='margin-left:8px'>";
				while($r=mysql_fetch_array($friend)){
					echo "<td>".'<a href = "friend.php?request_sent='.$r['uid'].'">'.$r['uname']."</td>";
				}
				echo "</table>";
				?>
		
				<h1> Friends </h1>
				<?php
				$all_friend="(select uname from friendrequest f, user u where f.friend_id=u.uid and f.uid=$uid and status='Confirmed') union (select uname from friendrequest natural join user where friend_id=$uid and status='Confirmed')";
				echo "<table border='1' style='margin-left:8px'>";
				$allfriends = mysql_query($all_friend);
				while($a=mysql_fetch_array($allfriends)){
					echo "<td align='center'>".$a['uname']."</td>";
				}
				echo "</table>";
		
				mysql_close($con);
				?>

				</div>
				
		</div>
		
		<footer>
			<div class="wrapper">
				Jingo - A place to share
			</div>
		</footer>
		
	</body>
</html>