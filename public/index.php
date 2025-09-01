<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/input.php';
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/functions.php';
require __DIR__ . '/../app/storage.php';
require __DIR__ . '/../app/profile.php';

//function redirect($url, $code=302){ header('Location: '.$url, true, $code); exit; }

// ROUTE
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/' || $path === '/feed')      $action = 'feed';
elseif ($path === '/login')                   $action = 'login';
elseif ($path === '/register')                $action = 'register';
elseif ($path === '/logout')                  $action = 'logout';
elseif ($path === '/settings')                $action = 'settings';
elseif ($path === '/settings/avatar')         $action = 'settings_avatar';
elseif ($path === '/profile/edit')         $action = 'profile_edit';

elseif (preg_match('#^/user/(\d+)$#', $path, $m)) { $action='profile'; $_GET['id']=(int)$m[1]; }
else $action = $_GET['a'] ?? 'feed';

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check(post('csrf'))) die('CSRF error');

    if ($action === 'register') {
        $res = register_user(post('login'), post('email'), post('password'));
        if ($res === true) redirect('/feed');
        $error = $res;
    } elseif ($action === 'login') {
        $ok = login(post('login'), post('password'));
        if ($ok) redirect('/feed');
        $error = 'Неверный логин или пароль';
    } elseif ($action === 'settings_avatar') {
        $uid = current_user_id();
        $res = save_uploaded_avatar($_FILES['avatar'] ?? [], (int)$uid);
        if ($res === true) redirect('/feed');
        $error = is_string($res) ? $res : 'Ошибка загрузки';
        $action = 'settings';
    }
     elseif ($action === 'profile_edit') {
        $uid = (int) current_user_id();
        $res = profile_update($uid, [
            'name'      => post('name'),
            'gender'    => post('gender'),
            'birthdate' => post('birthdate'),
            'location'  => post('location'),
            'website'   => post('website'),
            'bio'       => post('bio'),
        ]);
        if ($res['ok']) {
            redirect('/user/' . $uid);
        }
        $error_list = $res['errors']; // массив
        // Чтобы показать форму с ошибками:
        $action = 'profile_edit';
    }
}

// ACTIONS без вывода
if ($action === 'logout') { logout(); redirect('/login'); }

// GUARD
if (!in_array($action, ['login','register'])) login_required();

// RENDER
render('_header', ['title'=>'Retro Social']);
switch ($action) {
    case 'login':    render('auth_login',    ['error'=>$error??null]); break;
    case 'register': render('auth_register', ['error'=>$error??null]); break;
    case 'settings': render('settings',      ['error'=>$error??null]); break;
    case 'profile':  render('profile_view'); break;
    case 'feed':     render('feed_list');    break;
    case 'profile':      render('profile_view'); break;
    case 'profile_edit': render('profile_edit', ['errors'=>$error_list??[]]); break;
    default:         render('feed_list');    break;
}
render('_footer');
