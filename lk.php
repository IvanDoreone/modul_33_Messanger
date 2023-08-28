<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <!-- Bootstrap CSS -->
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>main</title>
    <!-- Свои стили -->
	<style>
		body {
			background: #fcfcfc;
		}
	</style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>


<?php

session_start();
ini_set('display_errors', '1'); 
include('db.php');
include('roles.php');



if (isset($_SESSION['login'])) {
$a = $_SESSION['login'];
}
if (isset($_COOKIE['user_hash_cookie'])) {
$cookie_hash = $_COOKIE['user_hash_cookie'];
    $req = "SELECT `login`, `id` FROM `users2` WHERE `user_hash_cookie`='$cookie_hash'";
    $res = $db->query($req);
    $data = $res->Fetch(PDO::FETCH_ASSOC);
    $my_id = $data['id'];
} else {

    $req = "SELECT `login`, `id` FROM `users2` WHERE `login`='$a'";
    $res = $db->query($req);
    $data = $res->Fetch(PDO::FETCH_ASSOC);
    $my_id = $data['id'];

}

if (!isset($_SESSION['login']) && isset($_COOKIE['user_hash_cookie'])) {
    $a = $data['login'];
    }


    $req6 = "SELECT `user_id`, `status` , `user_name` FROM `user_status` WHERE `user_id`='$my_id'";
    $res = $db->query($req6);
    $data = $res->Fetch(PDO::FETCH_ASSOC);
    //echo 'ar_dump $data';
    //var_dump($data);
    if (!$data['user_id'] && isset($my_id)) {
    $req3 = "INSERT INTO `user_status` (`user_id`, `status`) VALUES ($my_id, 'online')";
    $res = $db->query($req3);
    } 
    
    if ($data['user_id'] && isset($my_id)) {
        $req4 = "UPDATE `user_status` SET `status` = 'online' WHERE `user_id`=$my_id";
    $res = $db->query($req4);
    $req5 = "UPDATE `user_status` SET `user_name` = '$a' WHERE `user_id`=$my_id";
    $res = $db->query($req5);

    }
    


// Select New Name & Foto
//echo($a);
//запрос к БД на наличие загруженного фото профиля
$req = "SELECT `id`, `login`, `role` FROM `users2` WHERE `login`='$a'";
$res = $db->query($req);
$data = $res->Fetch(PDO::FETCH_ASSOC);
//echo "<br>";
//var_dump($data);
$my_id = $data['id'];
//echo "<br>";

$req9 = "SELECT `user_name`, `user_foto` FROM `user_profile` WHERE `user_id`='$my_id'";
$res = $db->query($req9);
$data = $res->Fetch(PDO::FETCH_ASSOC);
//echo "<br>";
//var_dump($data);
if ($data['user_name'] != '') {
    $a = $data['user_name'];
    
}
if ($data['user_foto'] != '') {
    $my_foto = $data['user_foto'];
}
//echo($a);
//echo($my_id);

$req5 = "UPDATE `user_status` SET `user_name` = '$a' WHERE `user_id`= '$my_id'";
    $res = $db->query($req5);


?>

<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
		<h5 class="my-0 mr-md-auto">e1_messangere</h5>
        <h5 class="my-0 mr-md-auto">Добро пожаловать, <b><?php  echo $a ?></b> !</h5>
		<nav class="my-2 my-md-0 mr-md-3">
			<a class="p-2 text-dark" href="index.php">Выход в личный кабинет</a>
			
		</nav>
        
	

</div>

    <?php
    include('messanger/messanger.php');

?>
 

</div>

</body>
</html>
     
     
