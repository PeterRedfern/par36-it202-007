CREATE TABLE IT202_F2024_Usergames (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int NOT NULL,
    `game_id` int NOT NULL,
    `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`game_id`) REFERENCES `IT202_F2024_Games` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
    unique key (`game_id`, `user_id`)
)