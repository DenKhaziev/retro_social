
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title><?= isset($title)?e($title):'Retro Social' ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/assets/css/base.css">
<!--[if IE]><link rel="stylesheet" type="text/css" href="/assets/css/ie5.css"><![endif]-->
</head>
<body>
<div id="page">
    <div id="header">
        <h1><a href="/feed">Retro Social</a></h1>
        <ul id="nav">
            <li><a href="/feed">Главная</a></li>
            <?php if (current_user_id()): ?>
                <li><a href="/logout">Выход</a></li>
            <?php else: ?>
                <li><a href="/login">Вход</a></li>
                <li><a href="/register">Регистрация</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="content">
