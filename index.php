<?php 

session_start();
ini_set('display_errors', '1'); 
include('db.php');
include('roles.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>messanger</title>
    <!-- Bootstrap CSS (jsDelivr CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!-- Bootstrap Bundle JS (jsDelivr CDN) -->
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  
  

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript">

const reg_exp_email = '/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/';


// проверка на ввод email в обоих формах - регистрация и авторизация и защита от ввода кода
$(document).ready(function() { 
    $("#validEmail").blur(function(){
        var cheked_email = $("#validEmail").val();
        console.log(cheked_email);
        
        if (cheked_email.indexOf('@') < 1 || cheked_email.indexOf('.') < 1 || cheked_email.indexOf('>') > 0 || cheked_email.indexOf('<') > 0 || cheked_email.indexOf('/') > 0 || cheked_email.indexOf('?') > 0 || cheked_email.indexOf('"') > 0) {
        $('#submit_regg').attr('disabled', true);
            //alert('Введите корректный email');
            //$(".popup-open")[0].click();
            $("#modal1")[0].click();
              $('#validEmail').val() = '';
        } else {
            $('#submit_regg').attr('disabled', false);  
        }
});      
    });

    $(document).ready(function() { 
    $("#validEmail1").blur(function(){
        var cheked_email = $("#validEmail1").val();
        console.log(cheked_email);
        if (cheked_email.indexOf('@') < 1 || cheked_email.indexOf('.') < 1 || cheked_email.indexOf('>') > 0 || cheked_email.indexOf('<') > 0 || cheked_email.indexOf('/') > 0 || cheked_email.indexOf('?') > 0 || cheked_email.indexOf('"') > 0) {
        $('#submit_regg1').attr('disabled', true);
            //$(".popup-open")[0].click();
            $("#modal1")[0].click();
            $('#validEmail1').val() = '';
        } else {
            $('#submit_regg1').attr('disabled', false); 
        }
});      
    });
  </script>  

</head>
<body>


<!-- Кнопка-триггер модального окна -->
<button hidden id = "modal1" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Запустите  модальноe окнo
</button>

<!-- Модальное окно c предупреждением на ввод некорректного email -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Введите корректный email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
       в формате: example@gmail.com
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
    
      </div>
    </div>
  </div>
</div>




<?php if (empty($_POST)) {?>
    
    <?php }

if (isset($_POST["session_exit"])) {
    session_destroy();
    setcookie('user_hash_cookie', '', time());
    setcookie('login', '', time());
    $newcookie = NULL;
        $update_cookie->execute();
    header('location: index.php');
}


//запуск сессии, генерация токена и его запись в сессию, если еще не записан
if (!isset($_SESSION)) {
session_start();
}


if (isset($_COOKIE['login'])) {
$login = $_COOKIE['login'];
}

if (isset($_COOKIE['user_hash_cookie'])) {
$cookie = $_COOKIE['user_hash_cookie'];

$req = "SELECT `login`, `role` FROM `users2` WHERE `user_hash_cookie`='$cookie'";
$res = $db->query($req);
$data = $res->Fetch(PDO::FETCH_ASSOC);

}

// формируем токен для проверки формы
if (!isset($_POST["token"])) {
$token = hash('gost-crypto', random_int(0,999999));
}
if (isset($token) && empty($_SESSION['CSRF']) || !isset($_POST["token"])) {
   $_SESSION['CSRF'] = $token;
}



//форма аутентификации если токен еще не передан
if ( !isset($_POST["token"]) && !isset($data['login']) && !isset($_SESSION['vk']) && !isset($_SESSION['login'])) {
?>

<div class="container pt-4">
<div class="container pt-1">
<p>
  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample_1" aria-expanded="false" aria-controls="collapseExample">
  Авторизация через ВКонтакте
  </button>
</p>
<div class="collapse" id="collapseExample_1">
  <div class="card card-body">
  <h4>Авторизация через ВКонтакте</h4>
<form method="post" action="">
<input type="hidden" name="auth_vk" value="auth_vk">
<input type="checkbox" id="cookie" name="cookie_vk" value="remember" autocomplete="off">
<label for="cookie">Запомнить меня</label>
<input type="hidden" name="token" value="<?php echo $token ?>"> 
<input type="submit" value="Войти c VK" class="btn btn-primary">
</form> 
</div>
  </div>
</div>


<div class="container pt-1">
<p>
  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample">
  Вход для зарегистрированных пользователей
  </button>
</p>
<div class="collapse" id="collapseExample2">
  <div class="card card-body">
  <h4>Вход для зарегистрированных пользователей</h4>
<form method="post" action="">
<input type="text" name="login" placeholder="введите email" class="form-control" size= "30%" autocomplete="off" id = "validEmail"><br/>
<input type="password" name="pass" class="form-control" width = "100px" autocomplete="off"> <br/>
<input type="hidden" name="auth" value="auth">
<input type="checkbox" id="cookie" name="cookie1" value="remember" autocomplete="off">
<label for="cookie">Запомнить меня</label>
<input type="hidden" name="token" value="<?php echo $token ?>"> 
<input type="submit" value="Вход для зарегистрированных пользователей" class="btn btn-primary" id = "submit_regg">
</form>
</div>
  </div>
</div>



<div class="container pt-1">
<p>
  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample3" aria-expanded="false" aria-controls="collapseExample">
  Регистрация
  </button>
</p>
<div class="collapse" id="collapseExample3">
  <div class="card card-body" id = "validate">
  <h4>Регистрация</h4>
<form method="post" action="">
<input type="text" name="login_first" placeholder="введите email" id = "validEmail1" class="form-control" size= "30%" autocomplete="off"><br/>
<input type="password" name="pass_first" class="form-control" width = "100px" autocomplete="off" id = "pass_valid"> <br/>
<input type="checkbox" id="cookie" name="cookie2" value="remember" autocomplete="off">
<label for="cookie">Запомнить меня</label>
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" name="regg" value="regg>">
<input type="submit" value="Регистрация" class="btn btn-primary" id = "submit_regg1">
</form>
</div>
  </div>
</div>

<?php
}


//при РЕГИСТРАЦИИ
//проверка токена в POST из формы и запись логина и пароля если их еще нет в БД

if (isset($_POST["token"]) && $_POST["token"] == $_SESSION["CSRF"] && isset($_POST["regg"]) ) {
include('registration.php');
} 

if (isset($_POST["token"]) && $_POST["token"] != $_SESSION["CSRF"] && isset($_POST["regg"])) {
    echo 'токен не прошел проверку!';
}

// АВТОРИЗАЦИЯ
//проверка токена в POST из формы  и проверка логина и пароля
if (isset($_POST["token"]) && $_POST["token"] == $_SESSION["CSRF"] && (isset($_POST["auth"]))) {
include('authorization.php');
} 



// АВТОРИЗАЦИЯ VK
//проверка токена в POST из формы и запрос к ВК
if (isset($_POST["token"]) && $_POST["token"] == $_SESSION["CSRF"] && (isset($_POST["auth_vk"]))) {
    if (isset($_POST["cookie_vk"])) {
        
        $cookie_hash = md5(random_int(0,9999999)."solemio");
        if (empty($_COOKIE['user_hash_cookie'])) {
        setcookie('user_hash_cookie', $cookie_hash, time() + 3600*24*30);
        }
        if (empty($_SESSION['cookie']) && empty($_COOKIE['user_hash_cookie'])) {
            $_SESSION['cookie'] = $cookie_hash;
            }
         $cookie = $_SESSION['cookie'];
        }
        else {
            $newcookie = NULL; 
        }

    include('vk.php');
    $clientId     = '51661229'; // ID приложения
    $clientSecret = 'PkoiVEepJN7as6fxU52W'; // Защищённый ключ
    $redirectUri  = 'http://localhost:7888/modul27_autetification/index.php'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
 
// Формируем ссылку для авторизации
$params = array(
	'client_id'     => $clientId,
	'redirect_uri'  => $redirectUri,
	'response_type' => 'code',
	'v'             => '5.126', // (обязательный параметр) версиb API https://vk.com/dev/versions
 
	// Права доступа приложения https://vk.com/dev/permissions
	// Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
	// Если не указать "offline", то полученный токен будет жить 12 часов.
	'scope'         => 'photos,offline',
);
 
$a = 'http://oauth.vk.com/authorize?' . http_build_query( $params );

header('Location: '.$a);

} 

// при получении ответа от ВК
if (isset($_GET['code'])) {
    
    include('vk.php');
}
if ((!isset($_SESSION['token']) && isset($_SESSION['vk']))){
?>

    <h3>Вы не прошли авторизацию в ВК, вернитесь для повторной регистрации</h3>
    
<div class="container pt-4">
<form method="post" action="">

<input type="hidden" name="session_exit" value="session_exit"> <br/>
<input type="submit" value="Вернуться назад" class="btn btn-primary">
</form>
<div>
    <?php
if (isset($_POST["session_exit"])) {
session_destroy();
setcookie('user_hash_cookie', '', time());
setcookie('login', '', time());
header('location: index.php');
}
}

// Получение Имеми пользователя в ВК

if (isset($_SESSION['token']) ) {
$token = $_SESSION['token']; // Извлекаем токен из сессии
$userId = $_SESSION['user_id'];
// Формируем запрос на получение Имени и Фимилии из ВК
$params = array(
    'v' => '5.126', // Версия API
    'access_token' => $token, // Токен
    'user_ids' => $userId, // ID пользователей
    'fields' => 'photo_100,about' // Список опциональных полей https://vk.com/dev/objects/user
);
if (!$content = @file_get_contents('https://api.vk.com/method/users.get?' . http_build_query($params))) {
    $error = error_get_last();
    throw new Exception('HTTP request failed. Error: ' . $error['message']);
}
$response = json_decode($content);
// Если возникла ошибка
if (isset($response->error)) {
    throw new Exception('При отправке запроса к API VK возникла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
}
$response = $response->response;
foreach ($response as $userItem) {
    $userItem->id; // ID пользователя
    $userItem->first_name; // Имя
    $userItem->last_name; // Фамилия  
}
$first_name = $userItem->first_name;
$last_name = $userItem->last_name;
$vk_user_id = $userItem->id;
$_SESSION['login'] = $first_name;

if (isset($first_name)) {
$options = ['watch', 'edit'];
}

$_SESSION["status"] = 'run';

// обновление данных в БД
$req = "SELECT `login` FROM `users2` WHERE `vk_user_id`='$vk_user_id'";
$res_vk = $db->query($req);
$data_vk = $res_vk->Fetch(PDO::FETCH_ASSOC);
if (isset($data_vk['login'])) {
$login = $data_vk['login'];

$update_cookie2->execute();

if (!$data_vk) {

$login_vk = $first_name;
    $time = date('Y-m-d H:i:s',time());
    $role = 'vk_user';
    $cookie = $_SESSION['cookie'];
    $vk_user_id = $vk_user_id;

    $insertvk->execute();
}
}

    if (isset($newcoockie)) {
    //$login = $first_name;
    $update_cookie->execute();
    }
  
    if (empty($_COOKIE['login'])) {
        //setcookie('login', $data['login'], time() + 3600*24*30);
        };
    }


// приветствие при повторном входе по COOKIE (и перевод на обычное преветствие зарегистрированному пользователю)
if (isset($data['login']) && !isset($_SESSION['login'])) {

    if (!isset($_POST['auth']) && !isset($_POST['regg']) && !isset($first_name) ) {
    echo 'WELLCOME BACK, '.$data['login'] .' !';
    //include('lk.php');
    
    $_SESSION['login'] = $data['login'];
    $_SESSION["status"] = 'run';

    
}}


// Приветсвие зарегестрированному пользователю
if (isset($_SESSION['login']) ) {
$name_name = $_SESSION['login'];


//запрос к БД на наличие загруженного фото профиля
$req = "SELECT `id`, `login`, `role` FROM `users2` WHERE `login`='$name_name'";
$res = $db->query($req);
$data = $res->Fetch(PDO::FETCH_ASSOC);
//var_dump($data);
$my_id = $data['id'];
$my_login = $data['login'];
//echo($my_id);

$req6 = "SELECT `user_id`, `user_foto`, `user_name` FROM `user_profile` WHERE `user_id`='$my_id'";
$res = $db->query($req6);
$data = $res->Fetch(PDO::FETCH_ASSOC);
//echo "<br>";
//var_dump($data);
if (!$data['user_id']) {
$req3 = "INSERT INTO `user_profile` (`user_id`,  `user_name`) VALUES ($my_id, '$my_login')";
$res = $db->query($req3);
}
//if ($data['user_foto'] == '') {
//    $req7 = "UPDATE `user_profile` SET `user_foto` = 'images/2929594473.jpg' WHERE `user_id`=$my_id";
//    $res = $db->query($req7);
//}


$req9 = "SELECT `user_name` FROM `user_profile` WHERE `user_id`='$my_id'";
$res = $db->query($req9);
$data = $res->Fetch(PDO::FETCH_ASSOC);
//echo "<br>";
//var_dump($data);
if ($data['user_name'] != '') {
    $name_name = $data['user_name'];
}










    ?>
    <?php


if(isset($_POST['submit']) and $_FILES) {
move_uploaded_file($_FILES['foto']['tmp_name'], "images/".$_FILES['foto']['name']);
//echo "<br>";
//var_dump($_FILES);
//echo "<br>";
if ($_FILES['foto']['name'] !='') {
$my_foto = 'images/'.$_FILES['foto']['name'];

$req4 = "UPDATE `user_profile` SET `user_foto` = '$my_foto' WHERE `user_id`=$my_id";
$res = $db->query($req4);

}
}





// Вывод нового имени

if(isset($_POST['submit_name']) and $_POST['new_name']) { 
    $nam = $_POST['new_name'];
    $req = "SELECT `user_name` FROM `user_profile` ";
    $res = $db->query($req );
    $data = $res->fetchAll(PDO::FETCH_ASSOC);
    
    $names_for = [];
    for ($i=0; $i<count($data); $i++) {
        $names_for[] = $data[$i]['user_name'];
    }
    

    if( 

        in_array( $nam ,$names_for ) || strpos($nam, '>') || strpos($nam, '<'))
    {
    
    ?>
    <script>
        $(document).ready(function() { 
        $('#modal2')[0].click();
        })
    
    </script>
    <?php
    } else {
    $new_name =  $nam;
    $req8 = "UPDATE `user_profile` SET `user_name` = '$new_name' WHERE `user_id`=$my_id";
    $res = $db->query($req8);
    unset($_POST);
    header('location: index.php');
    }}

if (isset($_SESSION['login'])) {

    // Выыод нового фото профиля из базы
$req2 = "SELECT `user_foto` FROM `user_profile` WHERE `user_id`= $my_id";
$res = $db->query($req2);
$data = $res->Fetch(PDO::FETCH_ASSOC);
$new_foto = $data['user_foto'];
?>


<!-- Верхнее меню -->
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
		<h5 class="my-0 mr-md-auto">e1_messanger</h5>
        <h5 class="my-0 mr-md-auto">Добро пожаловать, <b><?php  echo $name_name ?></b> !</h5>
    
    <form method="post" action="">

    <input type="hidden" name="session_exit" value="session_exit"> <br/>
    <input type="submit" value="Выйти из аккаутна" class="btn btn-danger">
    </form>
    

	</div>
    <div class="py-5 text-center">	
            <h5>Выберете что делать:</h5>
    </div>
    


<!-- Кнопка-триггер модального окна -->
<button hidden id = "modal2" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal2">
  Запустите демо модального окна
</button>

<!-- Модальное окно c предупреждением о занятом имени -->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Имя уже занять</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        пожалуйста, выберете другое имя...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>







    <div class="container">
  <div class="row">
    <div class="col-sm">
    <p><a id = "collapse" class="btn btn-success" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Изменить фото профиля
</a>
</p>
<div class="collapse" id="collapseExample">
  	<div class="card card-body">
  	<form enctype="multipart/form-data" method="post">
   <p><input type="file" name="foto" accept="image/jpeg,image/png">
   <input type="submit" name = "submit" value="Отправить"></p>
  </form> 
  	</div>
	</div>
    <p>текущее фото профиля:</p>
    <p><img src="<?php 
    if ($new_foto) {
        echo $new_foto;
    } else {
        echo 'images/2929594473.jpg';
    }
    ?>" width = 75%></p>
    </div>



    <div class="col-sm">
    <p><a id = "collapse" class="btn btn-success" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="false" aria-controls="collapseExample2">
    Изменить имя профиля
</a>
</p>
<div class="collapse" id="collapseExample2">
  	<div class="card card-body">
    <form  method="post">
    <p>Введите новое имя</p>
   <p><input type="text" name="new_name" >
   <input type="submit" name = "submit_name" value="Отправить"></p>
  </form> 
  	</div>
	</div>
    </div>



    <div class="col-sm">
    <p><a id = "collapse" class="btn btn-success" data-toggle="collapse" href="#collapseExample3" role="button" aria-expanded="false" aria-controls="collapseExample3">
    Начните переписку:
</a>
</p>
<div class="collapse" id="collapseExample3">
  	<div class="card card-body">
  	<p><a href = "lk.php">go for it!</a></p>
  	</div>
	</div>
    
    </div>
  </div>
</div>


    <?php









}


//Код для кнопки Выйти из аккаунта
    

if (isset($_POST["session_exit"])) {
    session_destroy();
    setcookie('user_hash_cookie', '', time());
    setcookie('login', '', time());
    unset($login);
    unset($_POST);
    unset($data);
    unset($_SESSION);
    $newcookie = NULL;
        //$login = $login;
        $update_cookie->execute();
    header('location: index.php');
}

}
?>



<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>

</body>
</html>




