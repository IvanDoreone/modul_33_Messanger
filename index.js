// Подключение всех модулей к программе
var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server, {
    cors: {
      origin: "*"
    }
  }); // НЕ встявляем listen перед (server) - по новому обновлению пакета socket.io

  // Подключение к БД
  const mysql = require("mysql2");
  
  const connection = mysql.createConnection({
    host: "localhost",
    user: "root",
    database: "testtable",
    password: "root"
  });

// Отслеживание порта - сервер NODE JS будет работать на 3000 проту
server.listen(3000);

// Отслеживание url адреса и отображение нужной HTML страницы
app.get('/', function(request, respons) {
	respons.sendFile(__dirname + '/messanger/messanger.html');
});

// Массив со всеми пользователями - туда добавляются все пользователи в сети
users = [];
// Массив со всеми подключениями - туда добавляются все активные соединения
connections = [];

// Переходим на работу с библиотекой socket.io - обращения к методам объекта io

// Функция, которая сработает при подключении к странице - пользователь заходит на страницу
// Считается как новый пользователь
io.sockets.on('connection', function(socket) {
	console.log("Успешное соединение");
    
    var my_name;
    
	connections.push(socket);

    io.sockets.emit('connec' , socket.id);
console.log(socket.id);



	// Функция, которая срабатывает при отключении от сервера
	socket.on('disconnect', function(data) {

        
        const sql = "UPDATE `user_status` SET `status` = 'ofline' WHERE `socket_id` = '" + socket.id +"'";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
            //console.log(results);
        });
        let disko = '';
        const sql2 = "select `user_name` from  `user_status` WHERE `socket_id` = '" + socket.id +"'";
        connection.query(sql2, function(err, results) {
            if(err) console.log(err);
            //console.log(results);
           disko = results;
        });
        

        // Удаления пользователя из массива
		connections.splice(connections.indexOf(socket), 1);
        io.sockets.emit('disconnec' , {socket_id: socket.id, name_disconected: disko});
		console.log("Отключились:  " + socket.id);

	});

	// Функция получающая сообщение от какого-либо пользователя
	socket.on('send mess', function(data) {
		// Внутри функции мы передаем событие 'add mess',
		// которое будет вызвано у всех пользователей и у них добавиться новое сообщение (отключено)
        console.log(data);
        if (data.room == '') {
           // io.sockets.emit('add mess', {mess: data.mess, name: data.name, className: data.className, foto: data.foto});
        } 
        if (data.room !== '' ) {
        //socket.to(data.room).emit('add mess', {mess: data.mess, name: data.name, className: data.className, room: data.room, foto: data.foto});
        socket.to(data.room).emit('add mess', {mess: data.mess, name: data.name, className: data.className, room: data.room, foto: data.foto});
        }
        
        let arr =  data.chat_ids;
        console.log(arr);

        // Блок записи сообщений в БД
        for (let i = 0; i <= arr.length - 1; i++) {
            if (data.my_id_my != arr[i] && (typeof arr[i]) == 'string' || (typeof arr[i]) == 'number' && (data.my_id_my !=arr[i])) {
            const sql = "INSERT INTO `messages` (`message`, `sender`, `addressat`, `sender_classname`, `sender_foto`, `sender_name`, `addressat_name`) VALUES ('" + data.mess +"', '" + data.my_id_my +"', '" + arr[i] +"', '" + data.className +"', '" + data.foto +"' , '" + data.name +"', '" + data.adressant +"')";
            
         
            connection.query(sql, function(err, results) {
                if(err) console.log(err);
                
            });
        }}   
	});


    // Блок пересылки имени подключившегося контакта
    socket.on('set name', function(data) {
        console.log(data);
        
        socket.broadcast.emit('give name', data);

        const sql = "UPDATE `user_status` SET `socket_id` = '" + data.socket_id +"', `status` = 'online' WHERE `user_name` = '" + data.name +"'";
        
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
         

    });



// Блок действий при создании комнаты
    socket.on('create room', function(data) {
        console.log('data create room');
        console.log(data);
        //io.sockets.emit('give name', data.name);
        socket.join(data.room);
        io.to(data.name).emit('join room', {room: data.room, writer: data.writer, chat_ids: data.chat_ids});
        


    });

    // Добавление контака в комнату
    socket.on('agree to join room', function(data) {
        socket.join(data.room);
        
    });

    socket.on('create chat', function(data) {
        console.log(data);
        //io.sockets.emit('give name', data.name);
        socket.join(data.room);
data.name.forEach((name) => {
    io.to(name).emit('join room', {room: data.room, writer: data.writer, chat_ids: data.chat_ids});
  })
    });

    // Отключение от комнаты покинувшего чат
    socket.on('leave chat', function(data) {
        socket.leave(data.room);
        socket.to(data.room).emit('add mess', {mess: ' отключился  ', name: data.name, room: data.room, marker: 'marker'});
        
    });

    // Отключение от комнаты выбравшего другой контакт
    socket.on('leave room', function(data) {
        socket.leave(data.room);
        socket.leave(data.room);
        socket.to(data.room).emit('add mess', {mess: ' отключился  ', name: data.name, room: data.room, marker: 'marker'});
        data.room = '';
    });

// Пересылка сообщения
    socket.on('resend message', function(data) {
        //console.log(data);
        
        io.to(data.socket_id_resend).emit('add mess resend', {mess: data.message_sender_id, name: data.name_name, className: data.name_class, foto: data.name_foto});
            
// запись в БД пересланного сообщения
        const sql = "INSERT INTO `messages` (`message`, `sender`, `addressat`, `sender_classname`, `sender_foto`, `sender_name`, `addressat_name`) VALUES ('" + data.message_sender_id +"', '" + data.message_toresend +"', '" + data.id_resend +"', '" + data.name_class +"', '" + data.name_foto +"' , '" + data.name_name +"', '" + data.name_adessant +"')";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
    });


// Получение и запись отредактированного сообщения из истории сообщений
    socket.on('edit message', function(data) {
        console.log(data);
        
            
        
        const sql = "UPDATE `messages` SET `message` = '"+ data.new_text_eddited +"' WHERE `id` = '" + data.id_message_to_edit +"'";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
           
        });
    });
// Получение и запись отредактированного сообщения из новых сообщений
socket.on('edit new message', function(data) {
        console.log(data);
        let q = [];
        const sql2 = "Select `id` FROM `messages` WHERE `sender` = '" + data.sender_id +"' AND  `sender_name` = '" + data.sender_name +"' AND  `addressat_name` = '" + data.adresant_name +"' AND  `message` = '" + data.text_to_edit +"'  ";
        connection.query(sql2, function(err, results) {
            if(err) console.log(err);
            results.forEach(function(i) {
                q.push(i.id)
            })
            var max =  Math.max.apply(null, q);
const sql = "UPDATE `messages` SET `message` = '"+ data.new_text_eddited +"' WHERE `sender` = '" + data.sender_id +"' AND  `sender_name` = '" + data.sender_name +"' AND  `addressat_name` = '" + data.adresant_name +"' AND  `message` = '" + data.text_to_edit +"' AND  `id` = '" + max +"'  ";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
        });  
        io.to(data.address).emit('add mess resend', {mess: data.new_text_eddited, name: data.sendername, className: data.name_class, foto: data.name_foto});   
    });


// Удаление нового сообщения

    socket.on('delete new message', function(data) {
        console.log(data);
        let q = [];
        const sql2 = "Select `id` FROM `messages` WHERE `sender` = '" + data.sender_id +"' AND  `sender_name` = '" + data.sender_name +"' AND  `addressat_name` = '" + data.adresant_name +"' AND  `message` = '" + data.text_to_delete +"'  ";
        connection.query(sql2, function(err, results) {
            if(err) console.log(err);
            results.forEach(function(i) {
                q.push(i.id)
            })
            console.log(q);
            var max =  Math.max.apply(null, q);
const sql = "DELETE FROM `messages`  WHERE `sender` = '" + data.sender_id +"' AND  `sender_name` = '" + data.sender_name +"' AND  `addressat_name` = '" + data.adresant_name +"' AND  `message` = '" + data.text_to_delete +"' AND  `id` = '" + max +"'  ";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
        });  
        io.to(data.address).emit('add mess resend', {mess: data.new_text_eddited, name: data.sendername, className: data.name_class, foto: data.name_foto});   
    });


// Удаление  сообщения из истории сообщений
    socket.on('delete message', function(data) {
        console.log(data);
        
const sql = "DELETE FROM `messages`  WHERE `id` = '" + data.id_mess +"' ";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
         
           
    });

// Установка контакта для беззвучных сообщений
    socket.on('set mute', function(data) {
        console.log(data);
        
        const sql = "UPDATE `mutes` SET `mutes` = '" + data.mute +"' WHERE `user_id` = '" + data.my_id +"'";
        connection.query(sql, function(err, results) {
            if(err) console.log(err);
        });
          
    });



});