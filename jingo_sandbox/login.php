<?php
session_start();
ini_set('session.gc_maxlifetime',300);
//注销登录
if($_GET['action'] == "logout"){
    unset($_SESSION['uid']);
    unset($_SESSION['uname']);
    unset($_SESSION['state']);
    unset($_SESSION['curtime']);
	header("Location:index.html");
    exit;
	
}

//登录
if(!isset($_POST['login'])){
    exit('invalid visit!  <a href="index.html">please sign in here</a> ');
}
//$EMAIL = htmlspecialchars($_POST['EMAIL']);
$uname = $_POST['uname'];
$pw = $_POST['pw'];

//包含数据库连接文件
include('conn.php');
//检测用户名及密码是否正确
$check_query = mysql_query("select uid from user where uname='$uname' and pw='$pw' 
limit 1");

if($result = mysql_fetch_array($check_query)){
    //登录成功
    $_SESSION['uname'] = $uname;
    $_SESSION['uid'] = $result['uid'];
 
} else {
    exit('login fail! Click here <a href="javascript:history.back(-1);">back</a> try again');
}

mysql_close($con)
?>




<html>
	<head>
		<link rel="stylesheet/less" href="style.less">
		<title>Jingo</title>
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
				<img src="gfx/chairs.jpg">
				<div class="panel right">
					<h1>Personal profile:</h1>
				<?php
				include_once('conn.php');
				$uid=$_SESSION['uid'];
				
				$user_info = mysql_query("select * from user where uid=$uid");
				if($result=mysql_fetch_array($user_info)){
				    echo "<h4> Registration Information </h4>";
					echo "<p> <strong> User name: </strong> ".$result['uname']."</p>";
					echo "<p> <strong> Email: </strong> ".$result['email']."</p>";
					echo "<p> <strong> Registration date: </strong> ".$result['date']."</p>";
					echo "<h4> Personal Information </h4>";
					echo "<p> <strong> Name: </strong> ".$result['name']."</p>";
					echo "<p> <strong> Age: </strong> ".$result['age']."</p>";
					echo "<p> <strong> Cell phone: </strong> ".$result['phone']."</p>";		
				}
				mysql_close($con);
				?>
		
				</div>
				
			</div>
		</div>
		<footer>
			<div class="wrapper">
				Jingo - A place to share
			</div>
		</footer>
		
	</body>
</html>


