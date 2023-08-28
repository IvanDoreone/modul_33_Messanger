<?php
//session_start();
ini_set('display_errors', '1'); 
include('db.php');
include('roles.php');


// Select New Name & Foto

if (isset($_SESSION['login'])) {
	$a = $_SESSION['login'];
	}
	if (!isset($_SESSION['login']) && isset($_COOKIE['user_hash_cookie'])) {
	
		$cookie_hash = $_COOKIE['user_hash_cookie'];
		$req = "SELECT `login` FROM `users2` WHERE `user_hash_cookie`='$cookie_hash'";
		$res = $db->query($req);
		$data = $res->Fetch(PDO::FETCH_ASSOC);
		$a = $data['login'];
		}
	

//запрос к БД на наличие загруженного фото профиля
$req = "SELECT `id`, `login`, `role` FROM `users2` WHERE `login`='$a'";
$res = $db->query($req);
$data = $res->Fetch(PDO::FETCH_ASSOC);

$my_id = $data['id'];
$my_id_my = $data['id'];

$req10 = "SELECT * FROM `users2` ";
$res = $db->query($req10);
$data_main = $res->FetchALL(PDO::FETCH_ASSOC);

$req9 = "SELECT `user_name`, `user_foto` FROM `user_profile` WHERE `user_id`='$my_id'";
$res = $db->query($req9);
$data = $res->Fetch(PDO::FETCH_ASSOC);

if ($data['user_name'] != '') {
    $a = $data['user_name'];
}
if ($data['user_foto'] != '') {
    $my_foto = $data['user_foto'];
}


// Запрос all контактов из БД
$req10 = " SELECT * FROM `user_status`";
$res = $db->query($req10);
$data_status = $res->fetchAll(PDO::FETCH_ASSOC);
//var_dump($data_status);

$req9 = "SELECT * FROM `user_profile` ";
$res = $db->query($req9);
$data_names = $res->fetchAll(PDO::FETCH_ASSOC);

$req11 = "SELECT `mutes` FROM `mutes` WHERE `user_id` = '$my_id_my'";
$res = $db->query($req11);
$data_mutes = $res->fetch(PDO::FETCH_ASSOC);


$my_contact = '';
$name_to_write ='';
$room = '';
if (isset($_POST['choose_contact'])) {
	
	$my_contact = $_POST['choose_contact'];
	$req = "SELECT * FROM `user_status` WHERE `user_id` = $my_contact";
	$res = $db->query($req);
	$data = $res->fetch(PDO::FETCH_ASSOC);
	$name_to_write = $data['socket_id'];
	$room = $data['user_name'];	
	

	$req10 = " SELECT * FROM `messages` WHERE `sender` in ('$my_id' , '$my_contact') AND `addressat` in ('$my_id' , '$my_contact') order by `timestamp` DESC";
	$res = $db->query($req10);
	$data_mess = $res->fetchAll(PDO::FETCH_ASSOC);
	
}



?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<!-- Подключение Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
	<title>Чат программа</title>
	<!-- Свои стили -->
	<style>
		body {
			background: #fcfcfc;
		}
	</style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<span  hidden id = 'formute'><?php echo $data_mutes['mutes'] ?></span>
<!-- Модальное окно для выбора контакта для пересылки сообщения -->
<button hidden id = "button_choose_resend" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable">
  Запустить модальное окно
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalScrollableTitle">Кому переслать сообщение?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div id = 'choose' class="modal-body">
<div id="toresend"  class="d-grid gap-2">


<?php 
	for ($i=0; $i < count($data_status); $i++) { 
	if (isset($data_names[$i]['user_name'])) {
		($data_status[$i]['status'] == 'online') ? $style = 'green' : $style = 'grey';
	?>
<p id = '<?php echo($data_names[$i]['user_name']) ?>'><img id = '<?php echo($data_names[$i]['user_name']) ?>' class='<?php echo($data_status[$i]['socket_id']) ?>' src='<?php echo($data_names[$i]['user_foto']) ?>' width = '10%' alt='аватарка'><button name_adessant = '<?php echo($data_names[$i]['user_name']) ?> ' name_socket_id = '<?php echo($data_status[$i]['socket_id']) ?>' name_id = '<?php echo($data_main[$i]['id']) ?>' class='<?php echo($data_status[$i]['socket_id']) ?>' id = 'connact_to_resend' style='color: <?php echo($style) ?>;' value = '<?php echo($data_main[$i]['id']) ?>'> <?php echo($data_names[$i]['user_name']) ?> </button></p>
	
<?php
 }}
 ?>
 
   					</div>
      </div>
      <div class="modal-footer">
        
        <button hidden id = "resend_message_one" type="button" class="btn btn-primary">переслать</button>
      </div>
    </div>
  </div>
</div>

	

<!-- Модальное окно для редактирования сообщения -->
<button hidden id = "edit_message" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable2">
  Запустить модальное окно
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalScrollableTitle">Изменить текст сообщения:</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id = 'edit' class="modal-body">
	  
    <input style = "height: 50px; width:100%; border: 1px solid grey;" type = text id="text_to_edit"  name = "new_text_message" class="d-grid gap-2">
   	  
      </div>
      <div class="modal-footer">
        
        <button  id = "edit_message_one" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable2">Подтвердить</button>
      </div>
    </div>
  </div>
</div>






	<!-- Блок вывода контактов для создания группового чата -->
	<div class="container">
		<div class="py-5 text-center">
			<div id = 'notes' class="py-5 text-center">
			</div>
			
		</div>
		<div class="row">
		<div class="col-md-auto"  style="width: 200px">
		<div id = "chat2">
<p><a id = "collapse" class="btn btn-success" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
    Создать беседу
</a>
</p>
<div class="collapse" id="collapseExample">
  	<div class="card card-body">
  	<div  id = "chat3" class="d-grid gap-2">            
   					<div id="chat"  class="d-grid gap-2">


<?php 
	for ($i=0; $i < count($data_status); $i++) { 
	if (isset($data_names[$i]['user_name'])) {
		($data_status[$i]['status'] == 'online') ? $style = 'green' : $style = 'grey';
	?>
<p id = '<?php echo($data_names[$i]['user_name']) ?>'><img id = '<?php echo($data_names[$i]['user_name']) ?>' class='<?php echo($data_status[$i]['socket_id']) ?>' src='<?php echo($data_names[$i]['user_foto']) ?>' width = '35%' alt='аватарка'><button class='<?php echo($data_status[$i]['socket_id']) ?>' id = 'chat3' style='color: <?php echo($style) ?>;' value = '<?php echo($data_main[$i]['id']) ?>'> <?php echo($data_names[$i]['user_name']) ?> </button><b><span hidden class = 'span' id = 'span_"+ $next + "' style= 'color: red'></span></b></p>
	
<?php
 }}
 ?>
 
   					</div>
                </div>
				<p><button id = 'start_chat' class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> Начать чат </button></p>
  	</div>
	  </div>
	</div>	
<!-- Блок выбора контактов для переписки и включения.отключения беззвучного режима -->
	<div id = "people">
	<p>
  <a id = "collapse" class="btn btn-success" data-toggle="collapse" href="#collapseExample2" role="button" aria-expanded="true" aria-controls="collapseExample2">
    Выбрать контакт
  </a>
</p>
<div class="show" id="collapseExample2">
<form id="all_varns" method="POST" action="">
	<div id="all_varns"  class="d-grid gap-2"> 
	
	<?php 
	for ($i=0; $i < count($data_status); $i++) { 
	if (isset($data_names[$i]['user_name'])) {
		($data_status[$i]['status'] == 'online') ? $style = 'green' : $style = 'grey';
		(strpos($data_mutes['mutes'], $data_names[$i]['user_name']) !== false) ? $checked = 'checked' : $checked = '';
		(strpos($data_mutes['mutes'], $data_names[$i]['user_name']) !== false) ? $hidden = '' : $hidden = 'hidden';
	?>
<p class='<?php echo($data_status[$i]['socket_id']) ?>' id = '<?php echo($data_names[$i]['user_name']) ?>'><img id = '<?php echo($data_names[$i]['user_name']) ?>' class='<?php echo($data_status[$i]['socket_id']) ?>' src='<?php echo($data_names[$i]['user_foto']) ?>' width = '25%' alt='аватарка'><button class='<?php echo($data_status[$i]['socket_id']) ?>' id = 'people' style='color: <?php echo($style) ?>;'  name = 'choose_contact' value = '<?php echo($data_main[$i]['id']) ?>' data-toggle='collapse' href='#collapseExample2' role='button' aria-expanded='false' aria-controls='collapseExample2'> <?php echo($data_names[$i]['user_name']) ?> </button><b><span hidden class = 'span' id = 'span_"+ $next + "' style= 'color: red'></span></b> <img src = 'images\mute.png' width = 10% id = 'mute' name='<?php echo($data_names[$i]['user_name']) ?>' <?php echo($hidden) ?>><span id = 'mutee' name='<?php echo($data_names[$i]['user_name']) ?>'>mute</span><input type="checkbox" id="mute_check" name='<?php echo($data_names[$i]['user_name']) ?>' value="" <?php echo($checked) ?> > </p>
	
<?php
 }}
 ?>
</form>
    </div>
	</div>
	</div>
	</div>

			<div class="col-md-auto" style="width: 300px">
				<!-- Форма для получения сообщений и имени -->
				<h3>Форма сообщений</h3>
				<button hidden id = 'leave_chat' class="btn btn-warning"> Покинуть чат </button>
				<br>
				<form id="messForm">
					<input hidden type="text" name="name" id="name" placeholder="Введите имя" class="form-control" value = "<?php echo $a ?>">
					
					<?php
					(strpos($data_mutes['mutes'], $room) !== false) ? $check_2 = '' : $check_2 = 'hidden';
					(strpos($data_mutes['mutes'], $room) !== false) ? $check_3 = 'checked' : $check_3 = '';
					?>
					<p>текущий контакт: <b><span id = 'current_addresat'> <?php echo ($room) ?> <span></b> 
					<?php if ($room) {?>
					<img src = 'images\mute.png' width = 7% id = 'mute' name='<?php echo($room) ?>' <?php echo ($check_2) ?>><span id = 'mutee' name='<?php echo ($room) ?>'>mute</span>
                    <input class = 'current_mute' type="checkbox" id="mute_check" name='<?php echo ($room) ?>' value="" <?php echo($check_3) ?>></p>
					<?php }?>
					<label for="message">Сообщение</label>
					<textarea name="message" id="message" class="form-control" placeholder="Введите сообщение"></textarea>
					<br>
                    <label hidden for="message_private">Кому пишем?</label>
					<textarea hidden name="message_private" id="message_private" class="form-control" placeholder="получатель"></textarea>
					<br>
					<input type="button" value="Отправить" class="btn btn-danger" id = "megganger_transit">
					<br><br>
					<input type="submit" value="Очистить историю" class="btn btn-warning">
                    
				</form>
			</div>
			<div class="col-md-auto" style="width: 500px" >
				
			<!-- Вывод всех сообщений будет здесь -->
			<h3>Сообщения</h3>
				<div id="all_mess">
				<?php 
				if(isset($data_mess)) { ?>
				<div id="old_mess">
					<b>история сообщений:</b>
	<?php for ($i=0; $i < count($data_mess); $i++) { 
	if ($data_mess[$i]['sender'] != $data_mess[$i]['addressat'] ) {	
	?>
	<div oncontextmenu='return false' id ='new_message'  name = '<?php echo ($data_mess[$i]['sender'])?>' value = '<?php echo ($data_mess[$i]['message'])?>' class = '<?php echo ($data_mess[$i]['id'])?>'><div  class='alert alert-<?php echo ($data_mess[$i]['sender_classname'])?>' >
  <div class="dropdown">
  <button name = '<?php echo ($data_mess[$i]['id'])?>' style='width: 1px; height: 1px; opacity: 0;' class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a id = 'delete' name1 = '<?php echo ($data_mess[$i]['id'])?>' name2 = '<?php echo ($data_mess[$i]['sender'])?>' name3 = '<?php echo ($data_mess[$i]['message'])?>' name_foto = '<?php echo ($data_mess[$i]['sender_foto'])?>' name_name = '<?php echo ($data_mess[$i]['sender_name'])?>' name_class = '<?php echo ($data_mess[$i]['sender_classname'])?>' class="dropdown-item" href="#">Delete</a>
    <a id = 'edit' name1 = '<?php echo ($data_mess[$i]['id'])?>' name2 = '<?php echo ($data_mess[$i]['sender'])?>' name3 = '<?php echo ($data_mess[$i]['message'])?>' name_foto = '<?php echo ($data_mess[$i]['sender_foto'])?>' name_name = '<?php echo ($data_mess[$i]['sender_name'])?>' name_class = '<?php echo ($data_mess[$i]['sender_classname'])?>' class="dropdown-item" href="#">Edit</a>
    <a id = 'resend' name1 = '<?php echo ($data_mess[$i]['id'])?>' name2 = '<?php echo ($data_mess[$i]['sender'])?>' name3 = '<?php echo ($data_mess[$i]['message'])?>' name_foto = '<?php echo ($data_mess[$i]['sender_foto'])?>' name_name = '<?php echo ($data_mess[$i]['sender_name'])?>' name_class = '<?php echo ($data_mess[$i]['sender_classname'])?>' class="dropdown-item" href="#">Resend</a>
  </div>
</div><span hidden id = '<?php echo ($data_mess[$i]['id'])?>'></span><img src='<?php echo ($data_mess[$i]['sender_foto'])?>' width = '10%' alt='аватарка'><span id = '<?php echo ($data_mess[$i]['timestamp'])?>'> <?php echo ($data_mess[$i]['timestamp'])?> </span> <b><?php echo ($data_mess[$i]['sender_name'])?> </b>:<span id = 'message_span' name = '<?php echo ($data_mess[$i]['id'])?>'> <?php echo ($data_mess[$i]['message'])?></span></div></div>

<?php
 }}}
 ?>
					</div>
				</div>  
			</div>
	</div>

   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<!-- Подключаем jQuery, а также Socket.io -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


	<!-- Подключение к серверу socket !!! для локального теста замените на локальный ip устройства на котором будет запущен сервер (index.js) -->
	<!--<script src="http://192.168.1.77:3000/socket.io/socket.io.js"></script>-->
	<script src="http://localhost:3000/socket.io/socket.io.js"></script>
	<script>
    var my_name = 'ivan';
    var $megganger_transit = $("#megganger_transit");
    var now = new Date().toLocaleString();
    let chat_to_write = [];
	let chat_name = [];
	var $textarea = $("#message"); // Текстовое поле
            var $message_private = $("#message_private"); // Текстовое поле
			var $all_messages = $("#all_mess"); // Блок с сообщениями
            var $all_varns = $("#all_varns"); 
			var $chat = $("#chat");
			var $all_mess = $("#all_mess");
			var $old_mess = $("#old_mess");
            var $my_id = [];
            var $next_id = [];
            var $next = '';
            const people = [];
			var room = '';
			var $mymyid = '';
			var chat_ids = [];
			let number = 0;
			let number2 = 0;
			let muted = false;
			const mute_contact = [];
			
            
		// Блок управления звуковым сигналом
			const audio = new Audio("https://www.fesliyanstudios.com/play-mp3/387");
			var mutes = document.querySelectorAll('#mute_check')
			
	   
		
		// Блок случайного выбора цвета - класса для уведомлений в зависимости от sender
		var min = 1;
		var max = 6;
		var random = Math.floor(Math.random() * (max - min)) + min;

		// Устаналиваем класс в переменную в зависимости от случайного числа
		// Эти классы взяты из Bootstrap 4
		var alertClass;
		switch (random) {
			case 1:
				alertClass = 'secondary';
				break;
			case 2:
				alertClass = 'danger';
				break;
			case 3:
				alertClass = 'success';
				break;
			case 4:
				alertClass = 'warning';
				break;
			case 5:
				alertClass = 'info';
				break;
			case 6:
				alertClass = 'light';
				break;
			}

        

		// Функция для работы с данными на сайте
		$(function() {
			// Включаем socket.io и отслеживаем все подключения
			var socket = io.connect(':3000');
			console.log(socket);




// Блок для обработки отключения звука в сообщениях
			mutes.forEach(function(e) {
				if (e.checked) {
					mute_contact.push(e.name);
				} 

	   		e.onclick = () => {

		   var dinos = document.querySelectorAll('#mute')
		   dinos.forEach(function(i) {

			   var mutees = document.querySelectorAll('#mutee')
			
			   console.log(e)
		   	if (e.checked) {
	   		if(e.name == i.name) {
	   		i.removeAttribute('hidden');
	   
	   		muted = true;
	   		mute_contact.push(e.name);
	   		}
	    
   		} else {
	   	console.log('checkbox not checked')
	   	if(e.name == i.name) {
	   	i.setAttribute('hidden', 'true');
	   	console.log('mute_contact.indexOf(e)');
	   	console.log(mute_contact.indexOf(e.name));
	   	mute_contact.splice(mute_contact.indexOf(e.name), 1);
	   	} 
   		}
		})
		   
   socket.emit('set mute', {my_id: <?php echo $my_id_my?>, mute:  mute_contact})
   console.log('mute_contact');
		   
	   	}
	   
		})
		   



			// Делаем переменные:
			var $form = $("#messForm"); // Форму сообщений
			//var $form2 = $("#chat_form"); // Форму сообщений
            var $name = $("#name"); 
            if ($("#name").val() != '') {
            $name = $("#name").val();
            } else {
            $name = 'john';
            }
		
			


			let $my_contact_1 = '<?php echo ($my_contact)?>';
                let $name_to_write_1 = '<?php echo ($name_to_write)?>';
                let $room_1 = '<?php echo ($room)?>';
				

				$('#current_addresat').text($room_1);
				chat_ids.push($my_id);
				chat_ids.push($my_contact_1);
				name_to_write = $name_to_write_1;
				$("#message_private").val($name_to_write_1);
				
				// Задаем переменную массив с названием комнаты для сообщений
				room_new = [];
				room_new.push('<?php echo ($a) ?>');
				room_new.push($room_1);
				room_new.push('private');
				str = room_new.toString();
				room = str;
			
			// Передаем согласие на соединение с комнатой (если задан контакт выбором из списка контактов )	
			socket.emit('agree to join room', {room: str, chat_ids: chat_ids});



			// Очистка поля сообщений
			$form.submit(function(event) {
				event.preventDefault();
				$old_mess.empty();
			});



           // БЛОК Отправка Сообщений
		   $('#megganger_transit').on('click', function() {
			console.log(chat_ids);
			let text_to_regg = $textarea.val();
		if (text_to_regg.match(/[a-zA-Z,.?!;:+=-{}()&%$#!~'"а-яА-Я0-9]/) && !text_to_regg.match(/</) && !text_to_regg.match(/>/)) {
			let text_to_regg = $textarea.val();
		} else{
			let text_to_regg = '';
			alert('пожалуйста используйте в тексте буквы цифры и знаки препинания, не скрипт;)');
		}
  	socket.emit('send mess', {mess: text_to_regg, name: $name, className: alertClass, adress: $message_private.val(), room: room, adressant: $('#current_addresat').text(), foto: '<?php echo ($my_foto)?>', people: people, my_id: $my_id, chat_ids: chat_ids, my_id_my: '<?php echo ($my_id_my)?>'  });
				
				$all_messages.prepend("<div name = '"+ number +"' oncontextmenu='return false' id ='new_message' class='alert alert-secondary'><div class='dropdown'><button name = '"+ number +"' style='width: 1px; height: 1px; opacity: 0;' class='btn btn-secondary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></button><div class='dropdown-menu' aria-labelledby='dropdownMenuButton'><a id = 'delete' my_id_my = '<?php echo ($my_id_my)?>' my_id = '" + $my_id +"' address = '" + $message_private.val() +"'   name1 = 'message_id' name2 = '"+ $my_id +"'  name_adresant = '"+ $('#current_addresat').text() +"' name3 = '"+ $textarea.val() +"' name_foto = '<?php echo ($my_foto)?>' name_name = '" + $name + "' name_class = '"+ alertClass +"' class='dropdown-item' href='#'>Delete</a><a id = 'edit' my_id_my = '<?php echo ($my_id_my)?>' my_id = '" + $my_id +"' address = '" + $message_private.val() +"' name1 = 'message_id' name2 = '"+ $my_id +"' name3 = '"+ $textarea.val() +"' name_foto = '<?php echo ($my_foto)?>' name_name = '" + $name + "' name_class = '"+ alertClass +"' class='dropdown-item' href='#'>Edit</a><a id = 'resend' name1 = 'message_id' name2 = '"+ $my_id +"' name3 = '"+ $textarea.val() +"' name_foto = '<?php echo ($my_foto)?>' name_name = '" + $name + "' name_class = '"+ alertClass +"' class='dropdown-item' href='#'>Resend</a></div><img src='<?php echo ($my_foto)?>' width = '10%' alt='аватарка'><b> Я: </b><span id = 'message_span' name = '" + number + "'>" + $('#message').val() + "</span></div> </div>");
				$textarea.val('');
				
	//Блок дл обработки пересылки удаления и редактирования новых сообщений;
				number++;
				var mess = document.querySelectorAll('#new_message')
		mess.forEach(function(mes) {
				
				mes.oncontextmenu = function(){
				
				  console.log(mes);
				  let message_id = mes.getAttribute('name');
				  console.log(message_id);
				  
	let child1 = document.querySelectorAll('#new_message  div  button')
	
	for( let i = 0; i < child1.length; i++) {
	console.log(child1[i].getAttribute('name'));

	if (child1[i].getAttribute('name') == message_id) {
		console.log('match');
        child1[i].click();
	}
		let delete_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #delete');
		let edit_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #edit');
		let resend_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #resend');


		// Блок для Удаления сообщений
console.log('delete_buttons');
			console.log(delete_buttons);
			delete_buttons.forEach(function(delete_button) {
				delete_button.onclick = () => {
				var text_to_delete = delete_button.getAttribute('name3');
				var sender_messege_to_delete = delete_button.getAttribute('name2');
				var id_message_to_delete = delete_button.getAttribute('name1');
				var name_name = delete_button.getAttribute('name_name');
				var name_adresant = $('#current_addresat').text();
				var my_id = delete_button.getAttribute('my_id');
				var my_id_my = delete_button.getAttribute('my_id_my');
				var address = delete_button.getAttribute('address');
				var name_foto = delete_button.getAttribute('name_foto');
				var name_class = delete_button.getAttribute('name_class'); 
			
				var spans_to_delete = document.querySelectorAll('#all_mess  div');
				spans_to_delete.forEach(function(span_to_delete) {
				if (span_to_delete.getAttribute('name') == message_id) {
				
				console.log(span_to_delete);
				span_to_delete.remove();
				//span_to_edit.textContent = new_text_eddited;
				socket.emit('delete new message', {sender_name: name_name, text_to_delete: text_to_delete, adresant_name: name_adresant, sender_id: my_id_my, address: address, name_foto: name_foto, name_class: name_class});
				}
			})
				
				}
			})



// Блок для управления редактированием сообщений
console.log('edit_buttons');
			
			edit_buttons.forEach(function(edit_button) {
				edit_button.onclick = () => {
				var text_to_edit = edit_button.getAttribute('name3');
				var sender_messege_to_edit = edit_button.getAttribute('name2');
				var id_message_to_edit = edit_button.getAttribute('name1');
				var name_name = edit_button.getAttribute('name_name');
				var name_adresant = $('#current_addresat').text();
				var my_id = edit_button.getAttribute('my_id');
				var my_id_my = edit_button.getAttribute('my_id_my');
				var address = edit_button.getAttribute('address');
				var name_foto = edit_button.getAttribute('name_foto');
				var name_class = edit_button.getAttribute('name_class'); 


				$('#text_to_edit').val(text_to_edit);
				$('#edit_message').click();
				
				(document.querySelector('#edit_message_one')).onclick = () => {
					
				var new_text_eddited = $('#text_to_edit').val();
				var spans_to_edit = document.querySelectorAll('#new_message  div #message_span');
				
				spans_to_edit.forEach(function(span_to_edit) {
				if (span_to_edit.getAttribute('name') == message_id) {
				
				span_to_edit.textContent = new_text_eddited;
				if (span_to_edit.textContent.match(/[a-zA-Z,.?!;:+=-{}()&%$#!~'"а-яА-Я0-9]/) && !span_to_edit.textContent.match(/</) && !span_to_edit.textContent.match(/>/)) {
			let text_to_send = span_to_edit.textContent;
		} else{
			let text_to_send = text_to_edit;
			alert('пожалуйста используйте в тексте буквы цифры и знаки препинания, не скрипт;)');
		}

				socket.emit('edit new message', {sender_name: name_name, text_to_edit: text_to_edit, new_text_eddited: text_to_send, adresant_name: name_adresant, sender_id: my_id_my, address: address, name_foto: name_foto, name_class: name_class});
				}
			})
		}		
			}
		})



// Блок для пересылки сообщений
	resend_buttons.forEach(function(resend_button) {
	resend_button.onclick = () => {
	var id_message_toresend = resend_button.getAttribute('name1');
	
	var message_toresend = parseInt(<?php echo $my_id_my ?>);
	console.log(resend_button.getAttribute('name3'));
	var message_sender_id = resend_button.getAttribute('name3');
	var name_foto = resend_button.getAttribute('name_foto');
	var name_name = resend_button.getAttribute('name_name');
	var name_class = resend_button.getAttribute('name_class');
	var button_choose_resend = document.querySelector('#button_choose_resend');
	button_choose_resend.click();
	var connact_to_resend;
	let choose_buttons = document.querySelectorAll('#choose #toresend  p #connact_to_resend');
	
	choose_buttons.forEach(function(choose_button) {
		choose_button.onclick = () => {
			
var socket_id_resend = choose_button.getAttribute('name_socket_id');
var id_resend = choose_button.getAttribute('name_id');
var name_adessant = choose_button.getAttribute('name_adessant');

		socket.emit('resend message', {socket_id_resend: socket_id_resend, id_resend: id_resend, id_message_toresend: id_message_toresend, message_toresend: message_toresend, message_sender_id: message_sender_id, name_name: name_name, name_foto: name_foto, name_class: name_class, name_adessant: name_adessant});
		document.querySelector('#exampleModalScrollable').click();
			}
		})

			}
		})
			}

							
  return false
				}
 
		})				
	});
			

			// Блок отображения входящих сообщений
			socket.on('add mess', function(data) {
				console.log(data);
				// Встраиваем полученное сообщение в блок с сообщениями
				// У блока с сообщением будет тот класс, который соответвует пользователю что его отправил
				if (data.marker !=='marker') {
				$all_messages.prepend(now + "<div  oncontextmenu='return false' id ='new_message' class='alert alert-" + data.className + "'><img src='" + data.foto +"' width = '10%' alt='аватарка'> <b>" + data.name + " </b>: " + data.mess + "</div>");
				} 
				if (data.marker ==='marker') {
				$all_messages.prepend(data.name + " </b>: Отключился </b>");
				}
				console.log('document.querySelector');
				console.log(document.querySelector('#formute').textContent);
				let mutecont = document.querySelector('#formute').textContent;
				

				let current_mute = $('.current_mute');
				console.log(current_mute);
				console.log(current_mute.checked);

				if(!mutecont.includes(current_mute.checked) && !current_mute.checked) {
				audio.play();
				}


				
				var mess = document.querySelectorAll('#new_message')
			console.log('mess');
			console.log(mess);

			mess.forEach(function(mes) {
				
				// Вешаем событие клик
				mes.addEventListener('click', function(e) {
   
		  	})
  			})		
            });



		// Блок добавления пересланного сообщения 
			socket.on('add mess resend', function(data) {
				
				$all_messages.append(now + "<div id ='new_message' class='alert alert-" + data.className + "'><img src='" + data.foto +"' width = '10%' alt='аватарка'> <b>" + data.name + " </b>: " + data.mess + "</div>");
				
				var mess = document.querySelectorAll('#new_message')
			console.log('mess');
			console.log(mess);

			mess.forEach(function(mes) {
				
				// Вешаем событие клик
				mes.addEventListener('click', function(e) {
  
				 
		  	})
  			})		
            });


          // Блок добавления новых контактов
			var mess = document.querySelectorAll('#new_message');

			// Блок событий при подключении к серверу сокетов
            socket.on('connec', function(data) {
            
                if (!$my_id[0]){
                $my_id[0] = data;
                people.push($my_id[0]);
				$socket_id = $my_id[0];
                } else {
                $next_id[0] = data;
                people.push($next_id[0]);
				$socket_id = $next_id[0];
				let $socket_new = data;
                }


                if (!$my_id[1]){
                socket.emit('set name', {name: $name, foto: '<?php echo($my_foto)?>', socket_id: $socket_id, next_id: <?php echo ($my_id) ?>});
                $my_id[1] = $name;
                people.push($my_id[1]);
				

                } else {
                socket.on('give name', function(data) {
					console.log('data give name');
					console.log(data);

                $next = data.name;
				$next_id = data.next_id;
				$next_foto = data.foto;
                if ( $next) {
                
                if (!people.includes($next, 0)) {
                people.push($next);
				var $next_next_id = $next_id[0];


				var icons = document.querySelectorAll('#'+ $next)
                 icons.forEach (function(icon) {
					
					icon.remove();
					
				 });
 


				// Блок добавления новых контактов (не из БД) - залигинившихся после нашего входа в систему
                $all_varns.append("<div id ='"+ $next + "'><p class='"+ data.socket_id + "'><img class='"+  data.socket_id + "' src='" + $next_foto + "' width = '25%' alt='аватарка' ><button class='"+  data.socket_id + "' id = 'people' name = 'choose_contact' style='color: green;' value = '" + $next_id + "' data-toggle='collapse' href='#collapseExample2' role='button' aria-expanded='false' aria-controls='collapseExample2'>" + $next + "</button><b><span hidden class = 'span' id = 'span_"+ $next + "' style= 'color: red'></span></b></p></div>");
                $chat.append("<div id ='"+ $next + "'><p class='"+ data.socket_id + "'><img class='"+ data.socket_id + "' src='" + $next_foto + "' width = '25%' alt='аватарка'><button class='"+ data.socket_id + "' id = 'chat3' name = 'choose_contact' style='color: green;' value = '" + $next_id + "'>" + $next + "</button></p></div>"); 

				}
                }
                });
                }

			// Вывод массива people - имена контактов и их socket id
			console.log(people);
			$mymyid = people[0];
			});

			


			// Блок действий при отключении от сервера сокетов
            socket.on('disconnec', function(data) {	
			let name_dickonect = data['name_disconected'];
			let socket_dickonect = data['socket_id'];
			console.log(data);
				var icons = document.querySelectorAll('.'+ socket_dickonect);
                 icons.forEach (function(icon) {
					
					icon.style.color = 'grey';
					
					
				 });


				var index = people.indexOf(socket_dickonect);
				console.log(people);
				people.splice(index, 2);
				});



				// Скрипт для создания чата
				var btns_chat = document.querySelectorAll('#chat3')
				// Проходим по массиву
				btns_chat.forEach(function(btn) {
  				
  				btn.addEventListener('click', function(e) {
				(!chat_name.includes($name)) ? chat_name.push($name) : chat_name;
    			(!chat_to_write.includes(e.target.classList[0])) ? chat_to_write.push(e.target.classList[0]) : chat_to_write;
				(!chat_name.includes(e.target.innerText)) ? chat_name.push(e.target.innerText) : chat_name;
				(!chat_ids.includes(e.target.value)) ? chat_ids.push(e.target.value) : chat_ids;
				
				let str = chat_name.toString();
				//console.log(str);
          		var start_chat = document.getElementById('start_chat');
				start_chat.addEventListener('click', function() {
				let chat_name_x = '';
				//console.log(chat_name_x);
				let chat_to_write_x = [];
                //console.log(chat_to_write_x);
				chat_name_x = chat_name.toString();
				//chat_name_x = chat_name;
				chat_to_write_x = chat_to_write
                //console.log(str);
				room = str;
				var leave_chat = document.getElementById('leave_chat');
				if (room.indexOf('private') < 0) {
				leave_chat.removeAttribute("hidden");
				}
				start_chat.setAttribute("hidden", "true");
                var collapse = document.getElementById('collapse');
				collapse.click();
				
				$('#current_addresat').text(str);
				socket.emit('create chat', {name: chat_to_write_x, room: chat_name_x, writer: $name, chat_ids: chat_ids});
				})
				
  				})



			socket.on('join room', function(data) {
				
				socket.emit('agree to join room', {room: data.room, writer: data.writer, chat_ids: data.chat_ids});
				if (data.room.indexOf(',') > -1) {
				room = data.room;
				console.log(chat_ids);
				chat_ids = data.chat_ids;
				$('#current_addresat').text(data.room);
				
				var leave_chat = document.getElementById('leave_chat');
				if (room.indexOf('private') <= 0) {
					leave_chat.removeAttribute("hidden");
				start_chat.setAttribute("hidden", "true");
				}
				}
				

            });
  			})



				// Скрипт для выбора контакта для переписки
				
				var btns = document.querySelectorAll('#people')
				btns.forEach(function(btn) {
  				btn.addEventListener('click', function(e) {
				chat_ids = [];
    			let name_to_write = '';
				$all_mess.html('');
				socket.emit('leave room', {room: room, name: $name});
       			room = '';
				$('#current_addresat').text(e.target.innerText);
				var pars = parseInt(<?php echo $my_id_my ?>)
				chat_ids[0] = pars;
				chat_ids[1] = parseInt(e.target.value);
				console.log('chat_ids');
				console.log(chat_ids);
				console.log('pars');
				console.log(pars);
				name_to_write = e.target.classList[0];
				$("#message_private").val(name_to_write);
				console.log('e.target.classList');
				console.log(e.target.classList);
				console.log('e.target.classList[0]');
				console.log(e.target.classList[0]);
				let room_new = [];
				room_new.push('<?php echo ($a) ?>');
				room_new.push(e.target.innerText);
				room_new.push('private');
				let str = room_new.toString();
				room = str;
				
				socket.emit('create room', {name: e.target.classList[0], room: room, writer: $name, chat_ids: chat_ids});

				var leave_chat = document.getElementById('leave_chat');
				leave_chat.setAttribute("hidden", "true");

				
  				})


			// Блок ответа на запрос присоединения к комнате - присоединяется автоматически по запросу без подтверждения
			socket.on('join room', function(data) {
				
				socket.emit('agree to join room', {room: data.room});
				if (data.room.indexOf('private') > 0) {
				room = data.room;
				
				$('#current_addresat').text(data.writer);
			
				} else {
				$('#current_addresat').text(data.room);

				}

            });
  })


// Скрипт для выхода из чата
leave_chat.addEventListener('click', function() {
socket.emit('leave chat', {room: room, name: $name});
chat_name = [];
chat_to_write = [];
$all_mess.html('');
room = '';
chat_ids = [];
$('#current_addresat').text('не выбран');

leave_chat.setAttribute("hidden", "true");			
start_chat.removeAttribute("hidden");
})





// Блок для Удаления, пересылки и редактирования собщений из истории сообщений
var mess = document.querySelectorAll('#new_message')
mess.forEach(function(mes) {
				
				mes.oncontextmenu = function(){
				
				  console.log(mes.className);
				  let message_id = mes.className;
				  console.log(mes.getAttribute('value'));
				  console.log(mes.getAttribute('name'));
				  let mess_name = mes.getAttribute('name');
if(<?php echo $my_id_my ?> == mess_name || mess_name.indexOf(<?php echo $my_id_my ?>) > 0) {
	
	let child = document.querySelectorAll('#new_message  div  button')
for( let i = 0; i < child.length; i++) {
	
	if(child[i].getAttribute('name') == message_id) {
        child[i].click();
		let delete_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #delete');
		let edit_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #edit');
		let resend_buttons = document.querySelectorAll('#new_message  div  button ~ .dropdown-menu #resend');



// Блок для Удаления сообщений
console.log('delete_buttons');
			
			delete_buttons.forEach(function(delete_button) {
				delete_button.onclick = () => {
				var text_to_delete = delete_button.getAttribute('name3');
				var sender_messege_to_delete = delete_button.getAttribute('name2');
				var id_message_to_delete = delete_button.getAttribute('name1');
				var name_name = delete_button.getAttribute('name_name');
				var name_adresant = $('#current_addresat').text();
				var my_id = delete_button.getAttribute('my_id');
				var my_id_my = delete_button.getAttribute('my_id_my');
				var address = delete_button.getAttribute('address');
				var name_foto = delete_button.getAttribute('name_foto');
				var name_class = delete_button.getAttribute('name_class'); 

				var spans_to_delete = document.querySelectorAll('#new_message');
				
				spans_to_delete.forEach(function(span_to_delete) {
				if (span_to_delete.classList[0] == message_id) {
				span_to_delete.remove();
				socket.emit('delete message', {id_mess: message_id, sender_name: name_name, text_to_delete: text_to_delete, adresant_name: name_adresant, sender_id: my_id_my, address: address, name_foto: name_foto, name_class: name_class});
				}
			})
				
				}
			})



// Блок для управления редактированием сообщений
console.log('edit_buttons');
			console.log(edit_buttons);
			edit_buttons.forEach(function(edit_button) {
				edit_button.onclick = () => {
				var text_to_edit = edit_button.getAttribute('name3');
				var sender_messege_to_edit = edit_button.getAttribute('name2');
				var id_message_to_edit = edit_button.getAttribute('name1');
				var name_name = edit_button.getAttribute('name_name');
				
				$('#text_to_edit').val(text_to_edit);
				$('#edit_message').click();
				console.log(document.querySelector('#edit_message_one'));
				(document.querySelector('#edit_message_one')).onclick = () => {
					
				var new_text_eddited = $('#text_to_edit').val();
				var spans_to_edit = document.querySelectorAll('#new_message  div #message_span');
				spans_to_edit.forEach(function(span_to_edit) {
				if (span_to_edit.getAttribute('name') == id_message_to_edit) {
				
				span_to_edit.textContent = new_text_eddited;
				if (span_to_edit.textContent.match(/[a-zA-Z,.?!;:+=-{}()&%$#!~'"а-яА-Я0-9]/) && !span_to_edit.textContent.match(/</) && !span_to_edit.textContent.match(/>/)) {
			let text_to_send = span_to_edit.textContent;
		} else{
			let text_to_send = text_to_edit;
			alert('пожалуйста используйте в тексте буквы цифры и знаки препинания, не скрипт;)');
		}

				socket.emit('edit message', {id_message_to_edit: id_message_to_edit, sender_messege_to_edit: sender_messege_to_edit, text_to_edit: text_to_edit, new_text_eddited: text_to_send, name_name: name_name});
				}
			})
		}		
			}
		})


// Блок для управления пересылкой сообщений
		resend_buttons.forEach(function(resend_button) {
			resend_button.onclick = () => {
	var id_message_toresend = resend_button.getAttribute('name1');
	
	var message_toresend = resend_button.getAttribute('name2');
	
	var message_sender_id = resend_button.getAttribute('name3');
	var name_foto = resend_button.getAttribute('name_foto');
	var name_name = resend_button.getAttribute('name_name');
	var name_class = resend_button.getAttribute('name_class');
	var button_choose_resend = document.querySelector('#button_choose_resend');
	button_choose_resend.click();
	var connact_to_resend;
	let choose_buttons = document.querySelectorAll('#choose #toresend  p #connact_to_resend');
	
	choose_buttons.forEach(function(choose_button) {
		choose_button.onclick = () => {
			
var socket_id_resend = choose_button.getAttribute('name_socket_id');
var id_resend = choose_button.getAttribute('name_id');
var name_adessant = choose_button.getAttribute('name_adessant');

		socket.emit('resend message', {socket_id_resend: socket_id_resend, id_resend: id_resend, id_message_toresend: id_message_toresend, message_toresend: message_toresend, message_sender_id: message_sender_id, name_name: name_name, name_foto: name_foto, name_class: name_class, name_adessant: name_adessant});
		document.querySelector('#exampleModalScrollable').click();
		}
	})
		}
	})
		}

	}
	}
			
  		return false
		}
	})	

});




	</script>
</body>
</html>