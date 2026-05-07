-- ============================================================
-- Migration: 022_update_blogs_overall_in4.sql
-- Description: Cập nhật 5 bài viết blog hiện có với nội dung MTECH
-- ============================================================

-- ============================================================
-- 1. Xóa danh mục blog cũ và thêm danh mục mới
-- ============================================================
DELETE FROM `blog_categories`;

INSERT INTO `blog_categories` (`name`, `slug`, `sort_order`) VALUES
('Tin tức Công ty',        'tin-tuc-cong-ty',        1),
('Cập nhật Dự án',         'cap-nhat-du-an',         2),
('Tư vấn Kỹ thuật',        'tu-van-ky-thuat',        3);

-- ============================================================
-- 2. Xóa tags cũ và thêm tags mới
-- ============================================================
DELETE FROM `blog_tags`;

INSERT INTO `blog_tags` (`name`, `slug`) VALUES
('Năng lực công ty',        'nang-luc-cong-ty'),
('Thiết kế xây dựng',       'thiet-ke-xay-dung'),
('Giám sát thi công',       'giam-sat-thi-cong'),
('Quy hoạch 1/500',         'quy-hoach-1-500'),
('Xi măng Vĩnh Sơn',        'xi-mang-vinh-son'),
('Dự án Hòa Bình',          'du-an-hoa-binh'),
('Nông nghiệp CNC',         'nong-nghiep-cnc'),
('Tập đoàn Xuân Thiện',     'tap-doan-xuan-thien'),
('Thanh Hóa',               'thanh-hoa'),
('Luyện kim',               'luyen-kim'),
('Thiết kế cơ sở',          'thiet-ke-co-so'),
('Thép Xanh',               'thep-xanh'),
('Tối ưu hóa năng lượng',   'toi-uu-hoa-nang-luong'),
('Phát điện nhiệt dư',      'phat-dien-nhiet-du'),
('Tư vấn kỹ thuật',         'tu-van-ky-thuat');

-- ============================================================
-- 3. Cập nhật 5 bài viết hiện có (id 1-5)
-- ============================================================

-- Bài viết 1: Cập nhật nội dung
UPDATE `blogs` SET
    `title` = 'MTECH.JSC tiếp tục khẳng định vị thế với Chứng chỉ năng lực hoạt động xây dựng Hạng I',
    `slug` = 'mtech-chung-chi-nang-luc-hang-i',
    `image` = 'assets/images/blogs/mtech-hang-i.jpg',
    `excerpt` = 'Trải qua hơn 10 năm phát triển kể từ 2011, MTECH.,JSC tự hào sở hữu các chứng chỉ năng lực xây dựng Hạng I do Bộ Xây dựng cấp, chuyên thiết kế, thẩm tra và giám sát các công trình công nghiệp quy mô lớn, đặc biệt là nhà công nghiệp và vật liệu xây dựng.',
    `content` = '<p>Trải qua hơn 10 năm phát triển kể từ 2011, MTECH.,JSC tự hào sở hữu các chứng chỉ năng lực xây dựng Hạng I do Bộ Xây dựng cấp, chuyên thiết kế, thẩm tra và giám sát các công trình công nghiệp quy mô lớn, đặc biệt là nhà công nghiệp và vật liệu xây dựng.</p><p>Chứng chỉ này là minh chứng cho năng lực chuyên môn, kinh nghiệm dày dạn và uy tín của MTECH trong lĩnh vực tư vấn xây dựng.</p>',
    `category_id` = (SELECT id FROM blog_categories WHERE slug = 'tin-tuc-cong-ty' LIMIT 1),
    `status` = 1
WHERE `id` = 1;

-- Bài viết 2: Cập nhật nội dung
UPDATE `blogs` SET
    `title` = 'Phê duyệt Quy hoạch chi tiết 1/500 Nhà máy xi măng Vĩnh Sơn do MTECH tư vấn lập',
    `slug` = 'quy-hoach-nha-may-xi-mang-vinh-son',
    `image` = 'assets/images/blogs/xi-mang-vinh-son.jpg',
    `excerpt` = 'UBND huyện Lương Sơn vừa chính thức phê duyệt đồ án Quy hoạch chi tiết xây dựng tỷ lệ 1/500 Nhà máy xi măng Vĩnh Sơn (quy mô 38,7 ha) do MTECH làm đơn vị tư vấn. Dự án hướng tới nâng cấp công nghệ tiên tiến, tận dụng nhiệt khí thải, tiết kiệm năng lượng và bảo vệ môi trường.',
    `content` = '<p>UBND huyện Lương Sơn vừa chính thức phê duyệt đồ án Quy hoạch chi tiết xây dựng tỷ lệ 1/500 Nhà máy xi măng Vĩnh Sơn (quy mô 38,7 ha) do MTECH làm đơn vị tư vấn.</p><p>Dự án hướng tới nâng cấp công nghệ tiên tiến, tận dụng nhiệt khí thải, tiết kiệm năng lượng và bảo vệ môi trường. Đây là một bước tiến quan trọng trong phát triển ngành công nghiệp vật liệu xây dựng tại Hòa Bình.</p>',
    `category_id` = (SELECT id FROM blog_categories WHERE slug = 'cap-nhat-du-an' LIMIT 1),
    `status` = 1
WHERE `id` = 2;

-- Bài viết 3: Cập nhật nội dung
UPDATE `blogs` SET
    `title` = 'Bước tiến mới tại Dự án Chăn nuôi công nghệ cao Xuân Thiện Thanh Hóa 2 & 3',
    `slug` = 'du-an-chan-nuoi-cong-nghe-cao-xuan-thien',
    `image` = 'assets/images/blogs/chan-nuoi-xuan-thien.jpg',
    `excerpt` = 'Góp phần thúc đẩy nông nghiệp hiện đại, MTECH đã hoàn thành công tác lập Quy hoạch chi tiết 1/500 cho chuỗi dự án Chăn nuôi công nghệ cao Xuân Thiện Thanh Hóa 2 và 3 tại huyện Ngọc Lặc, với tổng diện tích hơn 200 ha, ứng dụng hệ thống xử lý môi trường khép kín.',
    `content` = '<p>Góp phần thúc đẩy nông nghiệp hiện đại, MTECH đã hoàn thành công tác lập Quy hoạch chi tiết 1/500 cho chuỗi dự án Chăn nuôi công nghệ cao Xuân Thiện Thanh Hóa 2 và 3 tại huyện Ngọc Lặc.</p><p>Dự án có tổng diện tích hơn 200 ha, ứng dụng hệ thống xử lý môi trường khép kín, đáp ứng các tiêu chuẩn quốc tế về chăn nuôi bền vững.</p>',
    `category_id` = (SELECT id FROM blog_categories WHERE slug = 'cap-nhat-du-an' LIMIT 1),
    `status` = 1
WHERE `id` = 3;

-- Bài viết 4: Cập nhật nội dung
UPDATE `blogs` SET
    `title` = 'Dấu ấn MTECH tại siêu dự án Tổ hợp Thép Xanh Xuân Thiện Nam Định',
    `slug` = 'du-an-thep-xanh-xuan-thien-nam-dinh',
    `image` = 'assets/images/blogs/thep-xanh-nam-dinh.jpg',
    `excerpt` = 'Với công suất thiết kế lên tới 7,5 triệu tấn sản phẩm/năm, dự án Thép Xanh Xuân Thiện Nam Định là một trong những dự án luyện kim trọng điểm mà MTECH tự hào tham gia tư vấn lập Báo cáo nghiên cứu khả thi, thiết kế cơ sở và hạ tầng kỹ thuật.',
    `content` = '<p>Với công suất thiết kế lên tới 7,5 triệu tấn sản phẩm/năm, dự án Thép Xanh Xuân Thiện Nam Định là một trong những dự án luyện kim trọng điểm mà MTECH tự hào tham gia tư vấn.</p><p>MTECH đã lập Báo cáo nghiên cứu khả thi, thiết kế cơ sở và hạ tầng kỹ thuật cho dự án này, góp phần thúc đẩy phát triển ngành công nghiệp thép xanh tại Việt Nam.</p>',
    `category_id` = (SELECT id FROM blog_categories WHERE slug = 'cap-nhat-du-an' LIMIT 1),
    `status` = 1
WHERE `id` = 4;

-- Bài viết 5: Cập nhật nội dung
UPDATE `blogs` SET
    `title` = 'Giải pháp tối ưu hóa năng lượng: Hệ thống phát điện nhiệt dư tại các nhà máy xi măng',
    `slug` = 'he-thong-phat-dien-nhiet-du-xi-mang',
    `image` = 'assets/images/blogs/phat-dien-nhiet-du.jpg',
    `excerpt` = 'Nhằm giải quyết bài toán năng lượng bền vững, MTECH đã tư vấn thành công hệ thống phát điện nhiệt dư tận dụng khí thải cho hàng loạt nhà máy lớn như Xi măng Nghi Sơn (18 MW), Xuân Thành (18 MW), Đồng Lâm (9 MW), giúp giảm chi phí sản xuất và bảo vệ môi trường.',
    `content` = '<p>Nhằm giải quyết bài toán năng lượng bền vững, MTECH đã tư vấn thành công hệ thống phát điện nhiệt dư tận dụng khí thải cho hàng loạt nhà máy lớn.</p><p>Các dự án bao gồm Xi măng Nghi Sơn (18 MW), Xuân Thành (18 MW), Đồng Lâm (9 MW), giúp giảm chi phí sản xuất và bảo vệ môi trường. Đây là giải pháp tiên tiến trong tối ưu hóa năng lượng cho ngành công nghiệp.</p>',
    `category_id` = (SELECT id FROM blog_categories WHERE slug = 'tu-van-ky-thuat' LIMIT 1),
    `status` = 1
WHERE `id` = 5;

-- ============================================================
-- 4. Xóa tags cũ và gán tags mới cho các bài viết
-- ============================================================

-- Xóa tags cũ của 5 bài viết
DELETE FROM `blog_tag_map` WHERE `blog_id` IN (1, 2, 3, 4, 5);

-- Gán tags mới cho bài viết 1: Năng lực công ty, Thiết kế xây dựng, Giám sát thi công
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) 
SELECT 1, id FROM blog_tags WHERE slug IN ('nang-luc-cong-ty', 'thiet-ke-xay-dung', 'giam-sat-thi-cong');

-- Gán tags mới cho bài viết 2: Quy hoạch 1/500, Xi măng Vĩnh Sơn, Dự án Hòa Bình
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) 
SELECT 2, id FROM blog_tags WHERE slug IN ('quy-hoach-1-500', 'xi-mang-vinh-son', 'du-an-hoa-binh');

-- Gán tags mới cho bài viết 3: Nông nghiệp CNC, Tập đoàn Xuân Thiện, Thanh Hóa
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) 
SELECT 3, id FROM blog_tags WHERE slug IN ('nong-nghiep-cnc', 'tap-doan-xuan-thien', 'thanh-hoa');

-- Gán tags mới cho bài viết 4: Luyện kim, Thiết kế cơ sở, Thép Xanh
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) 
SELECT 4, id FROM blog_tags WHERE slug IN ('luyen-kim', 'thiet-ke-co-so', 'thep-xanh');

-- Gán tags mới cho bài viết 5: Tối ưu hóa năng lượng, Phát điện nhiệt dư, Tư vấn kỹ thuật
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`) 
SELECT 5, id FROM blog_tags WHERE slug IN ('toi-uu-hoa-nang-luong', 'phat-dien-nhiet-du', 'tu-van-ky-thuat');
