-- Таблица постов в блогах
CREATE TABLE IF NOT EXISTS blog_posts (
                                          id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                          user_id     INT UNSIGNED NOT NULL,
                                          title       VARCHAR(200) NOT NULL,
    body        MEDIUMTEXT NOT NULL,
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    views_count INT UNSIGNED NOT NULL DEFAULT 0,
    is_draft    TINYINT(1) NOT NULL DEFAULT 0,
    visibility  TINYINT(1) NOT NULL DEFAULT 0,
    KEY idx_blog_posts_user_created (user_id, created_at),
    CONSTRAINT fk_blog_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Комментарии к постам
CREATE TABLE IF NOT EXISTS blog_comments (
                                             id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                             post_id    INT UNSIGNED NOT NULL,
                                             user_id    INT UNSIGNED NOT NULL,
                                             body       TEXT NOT NULL,
                                             created_at DATETIME NOT NULL,
                                             KEY idx_blog_comments_post_created (post_id, created_at),
    CONSTRAINT fk_blog_comments_post FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_blog_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;