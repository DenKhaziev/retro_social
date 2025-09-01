<?php
function render($tpl, $vars = []) {
    extract($vars, EXTR_SKIP);
    include __DIR__.'/../templates/'.$tpl.'.php';
}
function redirect($url) {
    header('Location: '.$url); exit;
}