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
require __DIR__ . '/../app/messages.php';
require __DIR__ . '/../app/friends.php';
require __DIR__ . '/../app/photo_likes.php';
require __DIR__ . '/../app/photo_comments.php';
require __DIR__ . '/../app/blog.php';


// ROUTE
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/' || $path === '/profile') {
    $action = 'profile';
    $uid = current_user_id();
    header('Location: /profile/' . $uid); // Редирект на профиль текущего пользователя
    exit; // Останавливаем выполнение скрипта, чтобы избежать дальнейших обработок
} elseif ($path === '/login') {
    $action = 'login';
} elseif ($path === '/register') {
    $action = 'register';
} elseif ($path === '/logout') {
    $action = 'logout';
} elseif ($path === '/settings') {
    $action = 'settings';
} elseif ($path === '/settings/avatar')  {
    $action = 'settings_avatar';
} elseif ($path === '/profile/edit') {
    $action = 'profile_edit';
} elseif ($path === '/photos/upload') {
    $action = 'photo_upload';
} elseif ($path === '/search') {
    $action = 'search';
} elseif ($path === '/messages') {
    $action = 'messages_index';
} elseif ($path === '/messages/send') {
    $action = 'messages_send';
} elseif ($path === '/friends/add') {
    $action = 'friends_add';
} elseif ($path === '/forgot') {
    $action = 'forgot';
} elseif ($path === '/reset') {
    $action = 'reset';
} elseif (preg_match('#^/photos/(\d+)/photo/(\d+)/likes$#', $path, $m)) {
    $action = 'photo_likes';
    $_GET['user_id'] = (int)$m[1];
    $_GET['photo_id'] = (int)$m[2];
} elseif (preg_match('#^/messages/(\d+)$#', $path, $m)) {
    $action = 'message_thread';
    $_GET['user_id'] = (int)$m[1];
} elseif (preg_match('#^/friends/(\d+)$#', $path, $m)) {
    $action = 'friends';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/profile/(\d+)$#', $path, $m)) {
    $action = 'profile';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/photos(?:/(\d+))?$#', $path, $m)) {
    $action = 'photo';
    if (!empty($m[1])) {
        $_GET['user_id'] = (int)$m[1];
    }
} elseif (preg_match('#^/friends/accept/(\d+)$#', $path, $m)) {
    $action = 'friends_accept';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/friends/decline/(\d+)$#', $path, $m)) {
    $action = 'friends_decline';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/photos/like/(\d+)$#', $path, $m)) {
    $action = 'photo_like';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/profile/show/(\d+)$#', $path, $m)) {
    $action = 'profile_edit';
    $_GET['id'] = (int)$m[1];
} elseif (preg_match('#^/photos/delete/(\d+)$#', $path, $m)) {
    $action = 'photo_delete';
    $_GET['photo_id'] = (int)$m[1];
} elseif (preg_match('#^/photos/(\d+)/photo/(\d+)/comments$#', $path, $m)) {
    $action = 'photo_comments';
    $_GET['user_id'] = (int)$m[1];   // владелец фото (для ссылки "назад")
    $_GET['photo_id'] = (int)$m[2];  // id фото
} elseif (preg_match('#^/photos/(\d+)/photo/(\d+)/comment/add$#', $path, $m)) {
    $action = 'photo_comment_add';
    $_GET['user_id']  = (int)$m[1];
    $_GET['photo_id'] = (int)$m[2];
} elseif (preg_match('#^/blog/(\d+)$#', $path, $m)) {
    $action = 'blog_index'; $_GET['user_id'] = (int)$m[1];
} elseif (preg_match('#^/blog/(\d+)/post/(\d+)$#', $path, $m)) {
    $action = 'blog_show'; $_GET['user_id'] = (int)$m[1]; $_GET['post_id'] = (int)$m[2];
} elseif ($path === '/blog/create') {
    $action = 'blog_create';
} elseif (preg_match('#^/blog/(\d+)/post/(\d+)/comments$#', $path, $m)) {
    $action = 'blog_comments'; $_GET['user_id'] = (int)$m[1]; $_GET['post_id'] = (int)$m[2];
} elseif (preg_match('#^/blog/(\d+)/post/(\d+)/comment/add$#', $path, $m)) {
    $action = 'blog_comment_add'; $_GET['user_id'] = (int)$m[1]; $_GET['post_id'] = (int)$m[2];
} elseif (preg_match('#^/blog/delete/(\d+)$#', $path, $m)) {
    $action = 'blog_delete'; $_GET['post_id'] = (int)$m[1];
} elseif (preg_match('#^/blog/like/(\d+)$#', $path, $m)) {
    $action = 'blog_like_toggle'; $_GET['post_id'] = (int)$m[1];

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
//        $uid = current_user_id();
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
    } elseif ($action === 'friends_add') {
        $userId = current_user_id();
        $friendId = (int)post('friend_id');

        // Проверка: не самому себе, не повторно
        if ($userId === $friendId || get_friendship_status($userId, $friendId)) {
            redirect('/profile/' . $friendId);
        }

        db_query("
            INSERT INTO friendships (user_id, friend_id, status, requester_id, created_at, updated_at)
            VALUES (?, ?, 'pending', ?, NOW(), NOW())
        ", [$userId, $friendId, $userId]);

        redirect('/profile/' . $friendId);
    } elseif ($action === 'photo_like') {
        $userId  = current_user_id();
        $photoId = (int)($_GET['id'] ?? 0);
        $row = db_query("SELECT user_id FROM photos WHERE id=? LIMIT 1", [$photoId])
            ->get_result()->fetch_assoc();
        $ownerId = $row ? (int)$row['user_id'] : 0;
        // на всякий
        if ($photoId <= 0) {
            redirect('/photos');
        }

        // Уже лайкал?
        $res = db_query("SELECT 1 FROM photo_likes WHERE photo_id=? AND user_id=? LIMIT 1", [$photoId, $userId])->get_result();
        if ($res->fetch_row()) {
            // снятие лайка
            db_query("DELETE FROM photo_likes WHERE photo_id=? AND user_id=? LIMIT 1", [$photoId, $userId]);
        } else {
            // поставить лайк
            db_query("INSERT INTO photo_likes (photo_id, user_id, created_at) VALUES (?, ?, NOW())", [$photoId, $userId]);
        }
        redirect('/photos/' . ($ownerId ?: current_user_id()));
    } elseif ($action === 'photo_upload') {
        $uid = current_user_id();
        $res = upload_photo($_FILES['photo'] ?? [], $uid);
        if ($res && $res[0] === '/') { redirect('/photos'); }
        $error = $res ?: 'Ошибка загрузки';
        $action = 'photos';
    } elseif ($action === 'messages_send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $senderId   = current_user_id();
        $receiverId = (int)post('receiver_id');
        $body       = trim(post('body'));

        if ($receiverId && $body !== '') {
            db_query("
            INSERT INTO messages (sender_id, receiver_id, body, created_at)
            VALUES (?, ?, ?, NOW())
        ", [$senderId, $receiverId, $body]);

            redirect("/messages/$receiverId");
        }

        $error = 'Сообщение не может быть пустым';
        $action = 'message_thread';
    } elseif ($action === 'photo_delete') {
        $userId  = current_user_id();
        $photoId = (int)($_GET['photo_id'] ?? 0);

        $res = photo_delete($photoId, $userId);

        if (!$res['ok']) {
            if ($res['error'] === 'not_found') {
                http_response_code(404);
                render('errors/404');
            } elseif ($res['error'] === 'forbidden') {
                http_response_code(403);
                render('errors/403');
            } else {
                http_response_code(500);
                render('errors/500');
            }
        }

        redirect('/photos/' . $userId);
    } elseif ($action === 'photo_comment_add') {
        $currentUserId = current_user_id();
        $ownerId = (int)($_GET['user_id'] ?? 0);
        $photoId = (int)($_GET['photo_id'] ?? 0);
        $body = post('body');

        $res = photo_add_comment($photoId, $currentUserId, $body);

        if (!$res['ok']) {
            if ($res['error'] === 'not_found') {
                http_response_code(404);
                render('errors/404');

            }
            // Пустой коммент? Можно просто редиректнуть без вставки
        }

        redirect('/photos/' . $ownerId . '/photo/' . $photoId . '/comments');
    }
    // BLOG
    elseif ($action === 'blog_create') { // POST создание
        $userId = current_user_id();
        $title  = trim((string)post('title'));
        $body   = trim((string)post('body'));
        $isDraft    = (int)(post('is_draft') ? 1 : 0);
        $visibility = (int)(post('visibility') ?? 0);
        if ($title !== '' && $body !== '') {
            db_query("INSERT INTO blog_posts (user_id, title, body, created_at, updated_at, is_draft, visibility)
                  VALUES (?, ?, ?, NOW(), NOW(), ?, ?)", [$userId,$title,$body,$isDraft,$visibility]);
        }
        redirect('/blog/' . $userId);
    }
    elseif ($action === 'blog_delete') {
        $userId = current_user_id();
        $postId = (int)($_GET['post_id'] ?? 0);
        $post = get_post($postId);
        if (!$post) { http_response_code(404); render('errors/404'); }
        if ((int)$post['user_id'] !== $userId) { http_response_code(403); render('errors/403'); }
        db_query("DELETE FROM blog_posts WHERE id=?", [$postId]); // каскадом удалит комменты/лайки по FK
        redirect('/blog/' . $userId);
    }
    elseif ($action === 'blog_like_toggle') {
        $userId = current_user_id();
        $postId = (int)($_GET['post_id'] ?? 0);
        toggle_post_like($postId, $userId); // app blog helper
        // вернуть к посту
        $post = get_post($postId);
        $ownerId = $post ? (int)$post['user_id'] : $userId;
        redirect('/blog/' . $ownerId . '/post/' . $postId);
    }
    elseif ($action === 'blog_comment_add') {
        $currentUserId = current_user_id();
        $ownerId = (int)($_GET['user_id'] ?? 0);
        $postId  = (int)($_GET['post_id'] ?? 0);
        $body    = post('body');
        add_post_comment($postId, $currentUserId, $body);
        redirect('/blog/' . $ownerId . '/post/' . $postId );
    }
    elseif ($action === 'forgot') {
        $email = trim((string)post('email'));

        // Ищем пользователя
        $stmt = db_query("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            // Генерим токен и TTL (1 час)
            $token   = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            db_query("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?", [$token, $expires, $user['id']]);

            // Ссылка
            $link = APP_BASE_URL . '/reset?token=' . urlencode($token);


            $subject = 'Сброс пароля';
            $body  = "Здравствуйте!\r\n\r\n";
            $body .= "Вы запросили сброс пароля на сайте " . parse_url(APP_BASE_URL, PHP_URL_HOST) . ".\r\n";
            $body .= "Перейдите по ссылке, чтобы задать новый пароль (ссылка активна 1 час):\r\n";
            $body .= $link . "\r\n\r\n";
            $body .= "Если вы не запрашивали сброс — просто игнорируйте это письмо.\r\n";

            // Отправляем (ошибки можно логировать, но ответ пользователю — одинаковый)
            send_mail_simple($email, $subject, $body);
        }

        // Всегда показываем одинаковый ответ (чтобы не палить, зарегистрирован email или нет)
        redirect('/forgot?sent=1');
    }
    elseif ($action === 'reset') {
        $token     = trim((string)post('token'));
        $password  = (string)post('password');
        $password2 = (string)post('password2');

        // базовые проверки
        if ($token === '') {
            render('auth_reset', ['token' => '', 'error' => 'Неверная ссылка.']);
            return;
        }
        if ($password === '' || $password !== $password2) {
            render('auth_reset', ['token' => $token, 'error' => 'Пароли пустые или не совпадают.']);
            return;
        }

        // хэш пароля
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // ОБНОВЛЯЕМ ПО САМОМУ ТОКЕНУ, с проверкой срока
        $stmt = db_query("
        UPDATE users
        SET password_hash = ?, reset_token = NULL, reset_expires = NULL
        WHERE reset_token = ? AND reset_expires > NOW()
        LIMIT 1
    ", [$hash, $token]);

        // проверяем, сработало ли
        if ($stmt && $stmt->affected_rows === 1) {
            // успех — пароль применён, токен очищен
            redirect('/login');
        } else {
            // не сработало: токен не найден/просрочен/уже использован
            render('auth_reset', ['token' => '', 'error' => 'Ссылка недействительна или устарела.']);
            return;
        }
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
if (!in_array($action, ['login', 'register', 'forgot', 'reset'])) login_required();

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
        $uid = $_GET['id'] ?? current_user_id();
        $profileData = get_user_profile($uid);
        render('settings', ['error' => $error ?? null, 'profileData' => $profileData['user']]);
        break;
    case 'profile':
        $uid = $_GET['id'] ?? current_user_id();
        $profileData = get_user_profile($uid);
        $sidebarStats = get_user_profile(current_user_id());
        render('profile_view', compact('uid', 'profileData', 'sidebarStats' ));
        break;
    case 'photo':
        $uid    = current_user_id();
        $userId = get_profile_user_id();
        $user = find_user($userId);
        $isOwner = ($uid === $userId);
        $photos = get_photos($userId);
        render('photos', compact('photos', 'uid', 'userId', 'isOwner', 'user'));
        break;
    case 'profile_edit':
        $uid = current_user_id();
        $profileData = get_user_profile($uid);
        render('profile_edit', ['profile' => $profileData, 'errors' => $error_list ?? []]);
        break;
    case 'search':
        $query = trim($_GET['query'] ?? '');
        if ($query) {
            $users = search_users($query);
            render('search', ['users' => $users]);
        } else {
            render('search', ['error' => 'Введите запрос для поиска.']);
        }
        break;
    case 'friends':
        $userId = (int)($_GET['id'] ?? current_user_id());
        $pending = get_pending_friend_requests($userId);
        $isOwner = ($userId === current_user_id());

        $user = find_user($userId);
        if (!$user) {
            http_response_code(404);
            render('errors/404');
            break;
        }

        $friends = get_user_friends($userId);
        render('friends_list', [
            'user' => $user,
            'friends' => $friends,
            'isOwner' => $isOwner,
            'pending' => $pending ,
        ]);
        break;
    case 'messages_index':
        $uid = current_user_id();
        $dialogs = get_user_dialogs($uid);
        render('messages_index', compact('dialogs'));
        break;
    case 'friends_accept':
        $userId = current_user_id();
        $requesterId = (int)($_GET['id'] ?? 0);

        db_query("
        UPDATE friendships SET status = 'accepted'
        WHERE user_id = ? AND friend_id = ? AND status = 'pending'
    ", [$requesterId, $userId]);

        redirect('/friends/' . $userId);
        break;
    case 'friends_decline':
        $userId = current_user_id();
        $requesterId = (int)($_GET['id'] ?? 0);

        db_query("
        UPDATE friendships SET status = 'declined'
        WHERE user_id = ? AND friend_id = ? AND status = 'pending'
    ", [$requesterId, $userId]);

        redirect('/friends/' . $userId);
        break;
    case 'message_thread':
        $currentUserId = current_user_id();
        $otherUserId = (int)($_GET['user_id'] ?? 0);
        // проверка, чтобы не писать самому себе
        if ($otherUserId === $currentUserId) {
            redirect('/messages');
        }

        $otherUser = find_user($otherUserId);
        $messages = get_messages_between($currentUserId, $otherUserId);

        mark_messages_as_read($currentUserId, $otherUserId);

        render('messages_thread', [
            'messages' => $messages,
            'otherUser' => $otherUser,
            'error' => $error ?? null,
        ]);
        break;
    case 'photo_likes':
        $currentUserId = current_user_id();
        $ownerId = (int)($_GET['user_id'] ?? 0);
        $photoId = (int)($_GET['photo_id'] ?? 0);
        $photo = get_photo_owner($photoId);
        if (!$photo || $photo['user_id'] !== $ownerId || $ownerId !== $currentUserId) {
            http_response_code(403);
            render('errors/403');
            break;
        }
        $likes = get_photo_likes($photoId);
        render('photo_likes', [
            'photo' => $photo,
            'likes' => $likes,
        ]);
        break;
    case 'photo_comments':
        $ownerId = (int)($_GET['user_id'] ?? 0);
        $photoId = (int)($_GET['photo_id'] ?? 0);

        $photo = photo_find($photoId); // из app/photos.php
        if (!$photo) {
            http_response_code(404);
            render('errors/404');
            break;
        }

        // Комменты видны всем, без проверки владельца
        $comments = get_photo_comments($photoId);

        render('photo_comments', [
            'photo'    => $photo,
            'owner_id' => $ownerId ?: (int)$photo['user_id'],
            'comments' => $comments,
        ]);
        break;
    case 'blog_index':
        $blogUserId = (int)($_GET['user_id'] ?? 0);
        $blogUser   = find_user($blogUserId);
        if (!$blogUser) { http_response_code(404); render('errors/404'); break; }
        $posts = get_user_posts($blogUserId);
        render('blog_index', ['user'=>$blogUser, 'posts'=>$posts]);
        break;

    case 'blog_show':
        $ownerId = (int)($_GET['user_id'] ?? 0);
        $postId  = (int)($_GET['post_id'] ?? 0);

        $post = get_post($postId);              // см. функцию ниже
        if (!$post || (int)$post['user_id'] !== $ownerId) {
            http_response_code(404);
            render('errors/404');
            break;
        }

        inc_post_view($postId, current_user_id());               // инкремент просмотров

        $comments = get_post_comments($postId); // список комментов
        $isOwner  = (current_user_id() === (int)$post['user_id']);

        render('blog_show', [
            'post'     => $post,
            'owner_id' => $ownerId,
            'comments' => $comments,
            'isOwner'  => $isOwner,
        ]);
        break;

    case 'blog_create':
        render('blog_create', ['error'=>$error ?? null]);
        break;
    case 'forgot':
        render('auth_forgot', ['sent' => $_GET['sent'] ?? null]);
        break;

    case 'reset':
        $token = trim($_GET['token'] ?? '');
        render('auth_reset', ['token' => $token, 'error' => $error ?? null]);
        break;

}
render('_footer');
