CREATE TABLE `<?= $prefix ?>kwmmb_bookings`
(
    `id` INT NOT NULL   AUTO_INCREMENT,
    `str_id`      VARCHAR(10)  NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `phone`       VARCHAR(15)  NOT NULL,
    `adults`      INT NOT NULL DEFAULT 0,
    `child_0_5`   INT NOT NULL DEFAULT 0,
    `child_6_12`  INT NOT NULL DEFAULT 0,
    `food`        INT NOT NULL DEFAULT 0,
    `date_start`  DATE NOT NULL DEFAULT '2015-07-06',
    `date_end`    DATE NOT NULL DEFAULT '2015-07-13',
    `item`        INT NOT NULL DEFAULT 0,
    `verified`    INT NOT NULL DEFAULT 0,
    `comment`     TEXT NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';