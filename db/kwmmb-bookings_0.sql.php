CREATE TABLE `<?= $prefix ?>kwmmb-bookings`
(
    `id` INT NOT NULL   AUTO_INCREMENT,
    `name`        VARCHAR(255) NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `phone`       VARCHAR(15)  NOT NULL,
    `order`       VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';