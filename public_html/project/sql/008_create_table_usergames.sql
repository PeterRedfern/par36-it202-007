CREATE TABLE SC_UserGames (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`id`) REFERENCES Games (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users (`id`),
    unique key (`game_id`, `user_id`)
)