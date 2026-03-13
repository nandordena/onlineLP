CREATE TABLE `user` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user` VARCHAR(50) NULL DEFAULT NULL COLLATE,
	`pass` VARCHAR(100) NULL DEFAULT NULL COLLATE,
	`googleAuth` VARCHAR(50) NULL DEFAULT NULL COLLATE,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `user_pass` (`user`, `pass`) USING BTREE,
	INDEX `googleAuth` (`googleAuth`) USING BTREE
);

CREATE TABLE `session` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user` VARCHAR(50) NULL DEFAULT NULL COLLATE,
	`session_key` VARCHAR(200) NULL DEFAULT NULL COLLATE,
	`created_at` TIMESTAMP NULL DEFAULT (now()),
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `user_sessionkey` (`user`, `session_key`) USING BTREE
);

CREATE TABLE `workspace` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL COLLATE,
	PRIMARY KEY (`id`) USING BTREE
);

CREATE TABLE `user_workspace` (
	`user_id` INT NOT NULL,
	`workspace_id` INT NOT NULL,
	PRIMARY KEY (`user_id`, `workspace_id`) USING BTREE,
	INDEX `workspace_id` (`workspace_id`) USING BTREE
)
;
