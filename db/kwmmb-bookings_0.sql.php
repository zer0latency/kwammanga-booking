CREATE TABLE `<?= $prefix ?>kwmmb-bookings`
(
    `id` INT NOT NULL   AUTO_INCREMENT,
    `str_id`      VARCHAR(10)  NOT NULL,
    `comfort`     VARCHAR(255) NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `phone`       VARCHAR(15)  NOT NULL,
    `adults`      INT NOT NULL DEFAULT 0,
    `child_0_5`   INT NOT NULL DEFAULT 0,
    `child_6_12`  INT NOT NULL DEFAULT 0,
    `item`        INT NOT NULL DEFAULT 0,
    `comment`     TEXT NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';