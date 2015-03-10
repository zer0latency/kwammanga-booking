CREATE TABLE `<?= $prefix ?>kwmmb_code`
(
    `id` INT NOT NULL   AUTO_INCREMENT,
    `phone`       VARCHAR(255) NOT NULL,
    `code`        VARCHAR(10) NOT NULL,
    `booking_id`  INT NOT NULL DEFAULT 0,
    `ip`          VARCHAR(15)  NOT NULL,
    `date_create` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';