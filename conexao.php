<?php

define('HOST', 'localhost');
define('USUARIO', 'math');
define('SENHA', 'mt1233211233mt');
define('DB', 'login');

$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('nao foi possivel se conectar');