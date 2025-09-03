<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/input.php';
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/functions.php';
require __DIR__ . '/../app/storage.php';
require __DIR__ . '/../app/profile.php';
require __DIR__ . '/../app/photo.php';


// ROUTE
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/' || $path === '/profile')      $action = 'profile';
elseif ($path === '/login')                      $action = 'login';
elseif ($path === '/register')                   $action = 'register';
elseif ($path === '/logout')                     $action = 'logout';
elseif ($path === '/settings')                   $action = 'settings';
elseif ($path === '/settings/avatar')            $action = 'settings_avatar';
elseif ($path === '/profile/edit')               $action = 'profile_edit';
//elseif ($path === '/photos')               $action = 'photo';
elseif ($path === '/photos/upload')  $action = 'photo_upload';
if (preg_match('#^/photos(?:/(\d+))?$#', $path, $m)) {
    $action = 'photo';
    if (!empty($m[1])) {
        $_GET['user_id'] = (int)$m[1];  // чужая галерея
    }
}
elseif (preg_match('#^/profile/(\d+)$#', $path, $m)) {
    $action = 'profile_edit';
    $_GET['id'] = (int)$m[1];
} else {
    $action = $_GET['a'] ?? 'profile';
}

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check(post('csrf'))) die('CSRF error');

    if ($action === 'register') {
        $res = register_user(post('login'), post('email'), post('password'));
        if ($res === true) redirect('/profile');
        $error = $res;
    } elseif ($action === 'login') {
        $ok = login(post('login'), post('password'));
        if ($ok) redirect('/profile');
        $error = 'Неверный логин или пароль';
    } elseif ($action === 'settings_avatar') {
        $uid = current_user_id();
        $res = save_uploaded_avatar($_FILES['avatar'] ?? [], (int)$uid);
        if ($res === true) redirect('/profile');
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
            redirect('/profile/' . $uid);  // заменили user на profile
        }
        $error_list = $res['errors'];  // массив
        // Чтобы показать форму с ошибками:
        $action = 'profile_edit';
    }
    elseif ($action === 'photo_upload') {
        $uid = current_user_id();
        $res = upload_photo($_FILES['photo'] ?? [], $uid);
        if ($res && $res[0] === '/') { redirect('/photos'); }
        $error = $res ?: 'Ошибка загрузки';
        $action = 'photos';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $uid = current_user_id();
    $result = upload_photo($_FILES['photo'], $uid);
    if ($result !== 'Ошибка загрузки файла' && $result !== 'Не удалось загрузить файл') {
        redirect('/photos');
    } else {
        $error = $result;
    }
}

// ACTIONS без вывода
if ($action === 'logout') { logout(); redirect('/login'); }

// GUARD
if (!in_array($action, ['login', 'register'])) login_required();

// RENDER
render('_header', ['title' => 'Retro Social']);
switch ($action) {
    case 'login':
        render('auth_login', ['error' => $error ?? null]);
        break;
    case 'register':
        render('auth_register', ['error' => $error ?? null]);
        break;
    case 'settings':
        render('settings', ['error' => $error ?? null]);
        break;
    case 'profile':
        $uid = $_GET['id'] ?? current_user_id();
        $profileData = get_user_profile($uid);
        render('profile_view', $profileData);
        break;
    case 'photo':
        $uid    = current_user_id();
        $userId = get_profile_user_id();
        $user = find_user($userId);
        $isOwner = ($uid === $userId);
        $photos = get_photos($userId);
        render('photos', compact('photos', 'uid', 'userId', 'isOwner'));
        break;
    case 'profile_edit':
        $uid = current_user_id();
        $profileData = get_user_profile($uid);
        render('profile_edit', ['profile' => $profileData, 'errors' => $error_list ?? []]);
        break;
    default:
        render('profile_view');
        break;
}
render('_footer');
