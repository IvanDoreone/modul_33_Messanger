### Практическое задание/Модуль 33 Группа 35
### Иван Дорофеев
### ПРОЕКТ МЕССЕНДЖЕР

### Описание:
+ В проекте разработан месенджер с использованием технологии Websocket (на сервере Node.js  и библиотеке Socket io).
+ регистрация и личный кабинет на странице index.php
+ мессенджер на странице: messanger/messanger.php
+ Вэбсокет сервер - в файле index.js работает на порту 3000
+ Все запросы к БД выполняет сервер index.js на Node.js

### ВАЖНО:
Для локального теста приложения замените в файле messanger/messanger.php адрес сервера websocket с lokalhost на локальный ip устройства на котором будет запущен файл index.js (строка 318):
```
<script src="http://localhost:3000/socket.io/socket.io.js"></script>
```
### Для теста с историей переписки: логин/пароль  e@e./12345

#### Функционал:
+ Система регистрации новых пользователей (по email и выбранному паролю), система регистрации через аккаунт в VK.
+ Система авторизации ранее зарегистрированных пользователей (по email и паролю), с возможностью запомнить пользователя в системе, срок жизни Cookie установлен на 1 месяц.
+ Зарегистрированные пользователи и их данные - в tasstable/users2
+ Логи неудачных авторизаций по логину и паролю - в файле authorization_errors.log
+ После регистрации пользователю предоставляется доступ в личный кабинет с возможностью:
- Изменить фото профиля (по умолчанию отображается default_photo из папки /images).
- Изменить имя пользователя ( в таком случае email будет скрыт).
- Перейти в месенджер и начать переписку.
+ При переходе в месенджер пользователь видит список всех контактов, кликнув на каждый контакт пользователь загружает историю соoбщений с данным контактом и получает возможность немедленно начать с ним переписку.
+ Так же контакты отображаются в блоке "Создать беседу" (открывается по соответствующей кнопке), в этом блоке можно создать групповой час с любым кол-вом участников.
+ Имена всех контактов, находящихся в данный момент в сети, отображаются зеленым шрифтом, при выходе их системы цвет шрифта меняется на серый.
+ Так же у всех контактов отображается фото профиля.
+ Текущий контакт (выбранный для переписки) отображается над полем для ввода сообщений.
+ В указанном выше поле и в списке контактов можно отключить/включить заново режим звукового оповещения для входящих сообщений для каждого конкретного контакта, отметив/сняв checkbox.
+ Сообщения отображаются справа в блоке сообщений.
+ У всех сообщений отображается время сообщения, фото профиля автора, его имя и текст сообщения, цвет блока для удобства выбирается случайным образом в начале переписки для каждого участника.
+ У всех соoбщений пользователя (его авторства) есть возможность по правому клику переслать сообщение, отредактировать или удалить его. Данная возможность присутствует для новых (текущих) сообщений так и соoбщений из истории сообщений с выбранным контактом.
+ Очистить историю сообщений (из поля сообщений, не их базы) можно соответсвующей кнопкой, при этом из базы они не удаляются.
+ При удалении своих сообщений из меню по правому клику на сообщении пользователя - сообщения так же удаляются из базы.
+ При редактировании сообщения - новый текст записывается в БД и получателю отправляется новое, отредактированное сообщение.

### Безопасность:
+ В формах регистрации и авторизации применен CSRF токен для отражения CSRF attack.
+ Валидация вводимого email, пароля и выбранного имени.
+ Проверка вводимого имени по регулярному выражению.
+ Проверка вводимых соoбщений по регулярному выражению для противодействия XSS.
+ Перевод данных передаваемых в БД формат, которым ограничены колонки в таблицах и последующий их перевод в string и int для обработки в файле для защиты от SQL-injection.
+ Так же для защиты от SQL-injection использованы максимально короткие и несоставные запросы.
+ Отсутствуют Get запросы к БД.

### База Данных (MySQL):
+ testtable, основная таблица: users2 (логин, хэш пароля, хеш cookie, время регистации)
+ дочерние таблицы (все ссылаются 1 внешним ключом на users2 'id'):
- users_prpfile (имя, фото)
- user_status (текущий статус - online/ofline)
- mutes (контакты для беззвучных уведомлений)
- messages (сообшения)
+ все таблицы проиндексированны
 
#### Файл с базой данных для проекта (testtable) в файле testtable.sql 
#### Файл с PDO запросами в файле db.php

### Файл roles.php - для разделения ролей пользователей или разделения контактов по группам (свои и общие) отключен для теста работы по всем контактам.


---
### Использованные технологии:
+ HTML5
+ CSS
+ PHP 8
+ boostrap 
+ JS
+ библиотеки Socket io, Express
+ модуль mysql2 для Node.js
+ Jquery




---
Иван Дорофеев &copy; 2023
License: [MIT](https://mit-license.org/)



