-- ============================================================
-- Migration: 008b_upload_awards_image.sql
-- Description: Cập nhật ảnh cho bảng awards từ assets/images/awards/
-- ============================================================

UPDATE `awards` SET `image` = 'assets/images/awards/GiaithuongCLQG.jpg'   WHERE `sort_order` = 1;
UPDATE `awards` SET `image` = 'assets/images/awards/ISO.jpg'               WHERE `sort_order` = 2;
UPDATE `awards` SET `image` = 'assets/images/awards/VCCI.jpg'              WHERE `sort_order` = 3;
UPDATE `awards` SET `image` = 'assets/images/awards/ISO14001.jpg'          WHERE `sort_order` = 4;
UPDATE `awards` SET `image` = 'assets/images/awards/saovangdv.jpg'         WHERE `sort_order` = 5;
UPDATE `awards` SET `image` = 'assets/images/awards/chungnhannhathau.jpg'  WHERE `sort_order` = 6;

-- Verify
SELECT id, name, certificate, image FROM `awards` ORDER BY sort_order ASC;
