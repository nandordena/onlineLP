ALTER TABLE `user_workspace`
	ADD CONSTRAINT `workspace_id` FOREIGN KEY (`workspace_id`) REFERENCES `workspace` (`id`) ON UPDATE CASCADE ON DELETE NO ACTION;
