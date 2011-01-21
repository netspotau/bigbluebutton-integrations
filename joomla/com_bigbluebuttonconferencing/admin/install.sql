DROP TABLE IF EXISTS `#__bbb`;

CREATE TABLE `#__bbb` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`meetingName` VARCHAR(50) NOT NULL,
	`meetingVersion` INT(5) NOT NULL DEFAULT 0,
	`moderatorPW` VARCHAR(40) NOT NULL,
	`attendeePW` VARCHAR(40) NOT NULL,
	`waitForModerator` VARCHAR(3) NOT NULL DEFAULT 'yes',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `#__bbb_settings`;

CREATE TABLE `#__bbb_settings` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(10) NOT NULL,
	`varValue` VARCHAR(100) NOT NULL,
	`opening` VARCHAR(30) NOT NULL,
	`closing` VARCHAR(100) NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__bbb_settings` (`name`,`varValue`,`opening`,`closing`) VALUES ('salt','','Salt of BigBlueButton server:','Can be found in /var/lib/tomcat6/webapps/bigbluebutton/WEB-INF/classes/bigbluebutton.properties'), ('url','','URL of BigBlueButton server:','eg. http://example.com/bigbluebutton/');