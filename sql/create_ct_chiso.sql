CREATE TABLE IF NOT EXISTS `ct_chiso` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ma_chi_so` BIGINT UNSIGNED NOT NULL,
    `id_user` INT NOT NULL,
    `id_khoaphong` INT NOT NULL,
    `nam` SMALLINT NOT NULL,
    `ky` TINYINT NOT NULL,
    `du_lieu` JSON NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ct_chiso_luot_nhap` (`ma_chi_so`, `id_khoaphong`, `nam`, `ky`),
    KEY `idx_ct_chiso_khoa` (`id_khoaphong`),
    KEY `idx_ct_chiso_nam` (`ma_chi_so`, `nam`),
    CONSTRAINT `fk_ct_chiso_ma_chi_so`
        FOREIGN KEY (`ma_chi_so`)
        REFERENCES `chi_so_chat_luong` (`ma_chi_so`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
