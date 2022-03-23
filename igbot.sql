

CREATE TABLE `users` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scraper` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
);

CREATE TABLE `task_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(36) NOT NULL DEFAULT '',
  `type` varchar(36) NOT NULL DEFAULT '',
  `details` varchar(36) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);