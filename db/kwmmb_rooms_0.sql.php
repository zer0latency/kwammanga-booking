CREATE TABLE `<?= $prefix ?>kwmmb_rooms`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255) NOT NULL,
    `count`           INT          NOT NULL,
    `price`           INT          NOT NULL,
    `item_id`         INT          NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Version: 0';