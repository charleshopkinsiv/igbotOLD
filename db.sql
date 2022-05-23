
CREATE TABLE `task_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `account` varchar(36) NOT NULL DEFAULT '',
    val_one varchar(64) NOT NULL DEFAULT '',
    val_two varchar(64) NOT NULL DEFAULT '',
    val_three varchar(64) NOT NULL DEFAULT '',
    `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);


CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(64) NOT NULL DEFAULT '',
    `prospect` tinyint(4) NOT NULL DEFAULT '0',
    `lead` tinyint(4) NOT NULL DEFAULT '0',
    `actions` varchar(512) NOT NULL DEFAULT '',
    `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `suspect` tinyint(4) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
);


CREATE TABLE sequences (
    `id` int() NOT NULL AUTO_INCREMENT,
    account int() NOT NULL DEFAULT 0,
    status VARCHAR(16) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    FOREIGN KEY (account) REFERENCES accounts(id)
);


CREATE TABLE sequence_actions (
    `id` int() NOT NULL AUTO_INCREMENT,
    sequence int() NOT NULL DEFAULT 0,
    action VARCHAR(32) NOT NULL DEFAULT '',
    days_after_signup VARCHAR(32) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    FOREIGN KEY (sequence) REFERENCES sequences(id),
    FOREIGN KEY (action) REFERENCES actions(id)
);


CREATE TABLE sequence_users (
    `id` int() NOT NULL AUTO_INCREMENT,
    sequence int() NOT NULL DEFAULT 0,
    user int() NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (sequence) REFERENCES sequences(id),
    FOREIGN KEY (user) REFERENCES users(id)
);


CREATE TABLE accounts (
    `id` int() NOT NULL AUTO_INCREMENT,
    email varchar(64) NOT NULL DEFAULT '',
    username varchar(64) NOT NULL DEFAULT '',
    password varchar(64) NOT NULL DEFAULT '',
    proxy varchar(32) NOT NULL DEFAULT '',
    status varchar(16) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
);


CREATE TABLE scrape_routine (
    `id` int() NOT NULL AUTO_INCREMENT,
    scraper_type varchar(32) NOT NULL DEFAULT '',
    details varchar(32) NOT NULL DEFAULT '',
    frequency varchar(16) NOT NULL DEFAULT '',
    status varchar(16) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
);


CREATE TABLE queues (
    `id` int() NOT NULL AUTO_INCREMENT,
    account int() NOT NULL DEFAULT 0,
    task varchar(32) NOT NULL DEFAULT '',
    val_one varchar(64) NOT NULL DEFAULT '',
    val_two varchar(64) NOT NULL DEFAULT '',
    val_three varchar(64) NOT NULL DEFAULT '',
    date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
