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

$name=$_POST['name'];
$age=$_POST['age'];
$phone=$_POST['phone'];


if(isset($_POST['name'])||isset($_POST['age'])||isset($_POST['phone'])){
	$info_update = "UPDATE user SET name='$name', age='$age', phone='$phone' where uid='$uid'";
	mysql_query($info_update);
}

?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
		<link rel="stylesheet/less" href="style.less">
		<title>Jingo profile</title>
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
		
		<div id="content">
			<div class="wrapper">
				<div class="panel left">
				
		<?php
		$user_info = mysql_query("select * from user where uid=$uid");
		if($result=mysql_fetch_array($user_info)){
		    echo "<h1> Personal Profile</h1>";
		    echo "<h4> Registration Information </h4>";
			echo "<p> <strong> user name: </strong> ".$result['uname']."</p>";
			echo "<p> <strong> email: </strong> ".$result['email']."</p>";
			echo "<p> <strong> registration date: </strong> ".$result['date']."</p>";
			echo "<h4> Personal Information </h4>";
			echo "<p> <strong> name: </strong> ".$result['name']."</p>";
			echo "<p> <strong> age: </strong> ".$result['age']."</p>";
			echo "<p> <strong> phone: </strong> ".$result['phone']."</p>";		
		}
		mysql_close($con);
		?>
	</div>
		
		<div class="panel right">
			<h1> Update Information </h1>
			<p>
				<form action="profile.php" method="post">
					<p> 
						<strong>name:</strong> <input type="text" name="name" value="<?php echo $result['name'];?>"/>
				
						<strong>age:</strong> <input type="text" name="age" value="<?php echo $result['age'];?>"/>
					 
						<strong>phone:</strong> <input type="text" name="phone" value="<?php echo $result['phone'];?>"/>
					
					</p>
					<p>
						<input type="submit" name="submit" value="Update" class="left" />
					</p>
				</form>
				
			</p>
		</div>
		
		
		</form>
		</div>
		<footer>
			<div class="wrapper">
				Jingo - A place to share
			</div>
		</footer>
	</body>
</html>