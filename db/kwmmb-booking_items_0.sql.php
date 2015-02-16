CREATE TABLE `<?= $prefix ?>kwmmb-booking_items`
(
    `id` INT NOT NULL   AUTO_INCREMENT,
    `name`        VARCHAR(255) NOT NULL,
    `description` TEXT         NOT NULL,
    `price`       SMALLINT     NOT NULL,
    `price_full`  SMALLINT     NOT NULL,
    `roominess`   SMALLINT     NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';