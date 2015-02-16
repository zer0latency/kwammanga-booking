ALTER TABLE `<?= $prefix ?>kwmmb-booking_items`
ADD latitude VARCHAR(100) NOT NULL,
ADD longitude VARCHAR(100) NOT NULL,
COMMENT = 'Version: 1';