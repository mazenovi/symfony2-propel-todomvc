
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- todo
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `todo`;

CREATE TABLE `todo`
(
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`content` TEXT,
	`done` TINYINT(1),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
