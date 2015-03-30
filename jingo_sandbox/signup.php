<?php
if(!isset($_POST['submit'])){
    exit('invalid visit!');
}
$uname = $_POST['uname'];
$pw = $_POST['pw'];
$email = $_POST['email'];
//注册信息判断
if(!preg_match('/^[\w\x80-\xff]{3,15}$/', $uname)){
    exit('User name length should be between 3 and 15 <a href="javascript:history.back(-1);">back</a>');
}
if(strlen($pw) < 1){
    exit('password length should be no less than 6 <a href="javascript:history.back(-1);">back</a>');
}
// if(!preg_match('/^w+([-+.]w+)*@w+([-.]w+)*.w+([-.]w+)*$/', $email)){
//     exit('email format is invalid <a href="javascript:history.back(-1);">back</a>');
// }
//包含数据库连接文件
include('conn.php');
//检测用户名是否已经存在

$uname_check = mysql_query("select uname from user where uname='$uname' limit 1");
if(mysql_fetch_array($uname_check)){
    echo 'error: uname  ',$uname,' already exist <a href="javascript:history.back(-1);">back</a>';
    exit;
}

$check_query = mysql_query("select email from user where email='$email' limit 1");
if(mysql_fetch_array($check_query)){
    echo 'error: email  ',$email,' already exist <a href="javascript:history.back(-1);">back</a>';
    exit;
}
//写入数据
//$password = MD5($password);
date_default_timezone_set('America/New_York');

$sql = "INSERT INTO user(uname,pw,email)VALUES('$uname','$pw','$email')";
if(mysql_query($sql,$conn)){
//echo $stime;
    exit('Sign up success , click here <a href="index.html">Sign in</a>');
} else {
    echo 'sorry, insert data failed：',mysql_error(),'<br />';
    echo 'click here <a href="javascript:history.back(-1);">back</a> try again';
}

mysql_close($conn)
?>
