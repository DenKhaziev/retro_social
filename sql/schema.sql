-- Схема Retro Social (совместимая)
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- users
CREATE TABLE IF NOT EXISTS users (
     id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     login         VARCHAR(32)  NOT NULL UNIQUE,
    email         VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar_path   VARCHAR(255) DEFAULT '',
    created_at    DATETIME NOT NULL,
    updated_at    DATETIME NOT NULL,
    INDEX idx_users_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- user_profiles (1:1 к users)
CREATE TABLE IF NOT EXISTS user_profiles (
     user_id     INT UNSIGNED PRIMARY KEY,
     name        VARCHAR(100) NOT NULL,
    gender      ENUM('male','female','other') DEFAULT NULL,
    birthdate   DATE DEFAULT NULL,
    location    VARCHAR(120) DEFAULT '',
    website     VARCHAR(190) DEFAULT '',
    bio         VARCHAR(255) DEFAULT '',
    updated_at  DATETIME NOT NULL,
    INDEX idx_user_profiles_name (name),
    FULLTEXT KEY ft_user_profiles_name_bio (name, bio),  -- MySQL 5.6+ на InnoDB
    CONSTRAINT fk_up_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- friendships (одна строка на пару; правило в коде: user_id < friend_id)
CREATE TABLE IF NOT EXISTS friendships (
       user_id      INT UNSIGNED NOT NULL,
       friend_id    INT UNSIGNED NOT NULL,
       status       ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
    requester_id INT UNSIGNED NOT NULL,
    created_at   DATETIME NOT NULL,
    updated_at   DATETIME NOT NULL,
    PRIMARY KEY (user_id, friend_id),
    KEY idx_friendships_friend_status (friend_id, status),
    KEY idx_friendships_requester (requester_id),
    CONSTRAINT fk_fr_user      FOREIGN KEY (user_id)      REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fr_friend    FOREIGN KEY (friend_id)    REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fr_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- messages (личка)
CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    body        TEXT NOT NULL,
    created_at  DATETIME NOT NULL,
    read_at     DATETIME DEFAULT NULL,
    KEY idx_messages_receiver_created (receiver_id, created_at),
    KEY idx_messages_sender_created   (sender_id, created_at),
    CONSTRAINT fk_msg_sender   FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- photos (галерея)
CREATE TABLE IF NOT EXISTS photos (
      id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_id     INT UNSIGNED NOT NULL,
      file_path   VARCHAR(255) NOT NULL,
    caption     VARCHAR(255) DEFAULT '',
    created_at  DATETIME NOT NULL,
    KEY idx_photos_user_created (user_id, created_at),
    CONSTRAINT fk_ph_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- photo_likes (1 лайк от юзера на фото)
CREATE TABLE IF NOT EXISTS photo_likes (
   photo_id   INT UNSIGNED NOT NULL,
   user_id    INT UNSIGNED NOT NULL,
   created_at DATETIME NOT NULL,
   PRIMARY KEY (photo_id, user_id),
    KEY idx_photo_likes_user_created (user_id, created_at),
    CONSTRAINT fk_pl_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pl_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- photo_comments
CREATE TABLE IF NOT EXISTS photo_comments (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  photo_id   INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  body       TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  KEY idx_photo_comments_photo_created (photo_id, created_at),
    KEY idx_photo_comments_user_created  (user_id, created_at),
    CONSTRAINT fk_pc_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pc_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (опционально) сессии
CREATE TABLE IF NOT EXISTS sessions (
    id         CHAR(64) PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    user_agent VARCHAR(255) DEFAULT '',
    ip_addr    VARBINARY(16) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    KEY idx_sessions_user (user_id),
    KEY idx_sessions_expires (expires_at),
    CONSTRAINT fk_sess_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
