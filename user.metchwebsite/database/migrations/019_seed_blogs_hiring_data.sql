-- ============================================================
-- Migration: 019_create_blogs_hiring_table.sql
-- Description: Seed data cho bảng tuyển dụng (hiring/recruitment)
-- Thêm 3 tags mới + 3 bài blog tuyển dụng + blog_details + tag_map
-- ============================================================

-- ------------------------------------------------------------
-- 1. Thêm 3 tags mới liên quan đến tuyển dụng
-- ------------------------------------------------------------
INSERT INTO `blog_tags` (`name`, `slug`) VALUES
('Tuyển Dụng', 'tuyen-dung'),
('Kỹ Sư', 'ky-su'),
('Cơ Hội Nghề Nghiệp', 'co-hoi-nghe-nghiep');

-- ------------------------------------------------------------
-- 2. Lấy ID của category "Tuyển Dụng" (giả sử user đã thêm)
-- Nếu chưa có, uncomment dòng dưới để thêm category trước:
-- ------------------------------------------------------------
-- INSERT INTO `blog_categories` (`name`, `slug`, `sort_order`) VALUES
-- ('Tuyển Dụng', 'tuyen-dung', 7);

-- ------------------------------------------------------------
-- 3. Thêm 3 bài blog tuyển dụng
-- Lưu ý: category_id = 7 (tuyển dụng), điều chỉnh nếu ID khác
-- ------------------------------------------------------------
INSERT INTO `blogs` (`title`, `slug`, `image`, `excerpt`, `content`, `category_id`, `author`, `status`, `views`, `created_at`, `updated_at`) VALUES
(
    'Tuyển Dụng Kỹ Sư Cơ Khí - Cơ Hội Thăng Tiến Tại MTECH',
    'tuyen-dung-ky-su-co-khi-mtech',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/blog_single.jpg',
    'MTECH tuyển dụng kỹ sư cơ khí lành nghề với mức lương cạnh tranh. Môi trường làm việc chuyên nghiệp, cơ hội thăng tiến rõ ràng. Ứng tuyển ngay để trở thành một phần của đội ngũ chuyên gia hàng đầu ngành gia công cơ khí.',
    '<p>MTECH tuyển dụng kỹ sư cơ khí lành nghề với mức lương cạnh tranh. Môi trường làm việc chuyên nghiệp, cơ hội thăng tiến rõ ràng. Ứng tuyển ngay để trở thành một phần của đội ngũ chuyên gia hàng đầu ngành gia công cơ khí.</p>',
    7, 'admin', 1, 0, NOW(), NOW()
),
(
    'Tuyển Dụng Công Nhân Hàn Chuyên Nghiệp - Thu Nhập Ổn Định',
    'tuyen-dung-cong-nhan-han-chuyen-nghiep',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/blog_img_02.jpg',
    'Chúng tôi đang tìm kiếm công nhân hàn có kinh nghiệm làm việc trong ngành chế tạo kim loại. Chế độ đãi ngộ tốt, bảo hiểm đầy đủ, đào tạo nâng cao tay nghề miễn phí. Hãy gia nhập MTECH để phát triển sự nghiệp bền vững.',
    '<p>Chúng tôi đang tìm kiếm công nhân hàn có kinh nghiệm làm việc trong ngành chế tạo kim loại. Chế độ đãi ngộ tốt, bảo hiểm đầy đủ, đào tạo nâng cao tay nghề miễn phí. Hãy gia nhập MTECH để phát triển sự nghiệp bền vững.</p>',
    7, 'admin', 1, 0, NOW(), NOW()
),
(
    'Tuyển Dụng Quản Lý Dự Án Công Nghiệp - Lương Thưởng Hấp Dẫn',
    'tuyen-dung-quan-ly-du-an-cong-nghiep',
    'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/recent-post3-1.jpg',
    'MTECH cần tuyển quản lý dự án công nghiệp có kinh nghiệm từ 3 năm trở lên. Yêu cầu khả năng lãnh đạo tốt, am hiểu quy trình sản xuất cơ khí. Chế độ lương thưởng theo năng suất, thưởng dự án hấp dẫn.',
    '<p>MTECH cần tuyển quản lý dự án công nghiệp có kinh nghiệm từ 3 năm trở lên. Yêu cầu khả năng lãnh đạo tốt, am hiểu quy trình sản xuất cơ khí. Chế độ lương thưởng theo năng suất, thưởng dự án hấp dẫn.</p>',
    7, 'admin', 1, 0, NOW(), NOW()
);

-- ------------------------------------------------------------
-- 4. Thêm blog_details chi tiết cho 3 bài tuyển dụng
-- ------------------------------------------------------------
INSERT INTO `blog_details` (`blog_id`, `full_content`, `meta_title`, `meta_description`, `reading_time`) VALUES
(
    (SELECT id FROM blogs WHERE slug = 'tuyen-dung-ky-su-co-khi-mtech' LIMIT 1),
    '<div class="blog-text">
        <p><strong>MTECH JSC</strong> - Công ty hàng đầu trong lĩnh vực gia công cơ khí chính xác và chế tạo thiết bị công nghiệp, đang tìm kiếm những <strong>kỹ sư cơ khí tài năng</strong> để gia nhập đội ngũ phát triển của chúng tôi.</p>
        
        <h3 class="f_600 title_color mt-4">Mô tả công việc</h3>
        <ul>
            <li>Thiết kế và phát triển các sản phẩm cơ khí theo yêu cầu khách hàng</li>
            <li>Lập trình và vận hành máy CNC, máy tiện, máy phay</li>
            <li>Kiểm soát chất lượng sản phẩm trong quá trình gia công</li>
            <li>Hỗ trợ đội ngũ sản xuất giải quyết các vấn đề kỹ thuật</li>
            <li>Tối ưu hóa quy trình gia công để nâng cao năng suất</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Yêu cầu ứng viên</h3>
        <ul>
            <li>Tốt nghiệp Đại học chuyên ngành Cơ khí, Chế tạo máy hoặc tương đương</li>
            <li>Có kinh nghiệm từ 2-5 năm trong lĩnh vực gia công cơ khí</li>
            <li>Thành thạo sử dụng phần mềm CAD/CAM (SolidWorks, AutoCAD, Mastercam)</li>
            <li>Hiểu biết về các loại vật liệu kim loại và quy trình gia công</li>
            <li>Có khả năng đọc hiểu bản vẽ kỹ thuật tiếng Anh là lợi thế</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Quyền lợi</h3>
        <ul>
            <li>Mức lương: <strong>15-25 triệu/tháng</strong> (tùy kinh nghiệm và năng lực)</li>
            <li>Thưởng theo dự án, thưởng hiệu suất hàng tháng</li>
            <li>Đóng BHXH, BHYT, BHTN đầy đủ theo quy định</li>
            <li>Xét tăng lương 2 lần/năm</li>
            <li>Cơ hội đào tạo nâng cao nghiệp vụ trong và ngoài nước</li>
            <li>Môi trường làm việc chuyên nghiệp, năng động, cơ hội thăng tiến rõ ràng</li>
        </ul>
        
        <div class="alert alert-info mt-4">
            <strong>Thông tin liên hệ:</strong><br>
            Ứng viên quan tâm vui lòng gửi CV về email: <a href="mailto:hr@mtechjsc.vn">hr@mtechjsc.vn</a><br>
            Hotline: <strong>0909-123-456</strong> (gặp Phòng Nhân sự)<br>
            Địa chỉ: KCN Biên Hòa 2, Đồng Nai
        </div>
    </div>',
    'Tuyển Dụng Kỹ Sư Cơ Khí - Lương 15-25 Triệu - MTECH JSC',
    'MTECH tuyển kỹ sư cơ khí lành nghề, lương 15-25 triệu, đào tạo nâng cao, cơ hội thăng tiến. Ứng tuyển ngay!',
    8
),
(
    (SELECT id FROM blogs WHERE slug = 'tuyen-dung-cong-nhan-han-chuyen-nghiep' LIMIT 1),
    '<div class="blog-text">
        <p><strong>MTECH JSC</strong> đang mở rộng quy mô sản xuất và cần tuyển gấp <strong>công nhân hàn chuyên nghiệp</strong> để đáp ứng nhu cầu đơn hàng ngày càng tăng. Đây là cơ hội tuyệt vời cho những người thợ hàn muốn làm việc trong môi trường công nghiệp hiện đại.</p>
        
        <h3 class="f_600 title_color mt-4">Vị trí tuyển dụng</h3>
        <p>Công nhân hàn MIG/MAG, TIG, hàn điện - Số lượng: <strong>10 người</strong></p>
        
        <h3 class="f_600 title_color mt-4">Nhiệm vụ chính</h3>
        <ul>
            <li>Thực hiện các công việc hàn chi tiết máy, khung sườn, bồn bể theo bản vẽ</li>
            <li>Hàn các loại vật liệu: thép carbon, inox, nhôm tùy yêu cầu</li>
            <li>Kiểm tra chất lượng mối hàn, đảm bảo đạt tiêu chuẩn kỹ thuật</li>
            <li>Bảo dưỡng thiết bị hàn và khu vực làm việc</li>
            <li>Tuân thủ nghiêm ngặt quy trình an toàn lao động</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Yêu cầu công việc</h3>
        <ul>
            <li>Nam giới, tuổi từ 22-45, sức khỏe tốt</li>
            <li>Có chứng chỉ hàn từ bậc 3/6 trở lên (hàn điện, MIG/MAG, TIG)</li>
            <li>Kinh nghiệm tối thiểu 1 năm trong ngành chế tạo cơ khí</li>
            <li>Biết đọc bản vẽ kỹ thuật cơ bản</li>
            <li>Chăm chỉ, trung thực, có tinh thần trách nhiệm cao</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Chế độ đãi ngộ</h3>
        <ul>
            <li>Lương cơ bản: <strong>10-15 triệu/tháng</strong> + phụ cấp + thưởng sản lượng</li>
            <li>Thu nhập thực tế: 12-18 triệu/tháng (tùy tay nghề)</li>
            <li>Làm việc 6 ngày/tuần, giờ hành chính</li>
            <li>Được cung cấp đầy đủ bảo hộ lao động</li>
            <li>Hỗ trợ chỗ ở cho người ở xa (khu nhà trọ công ty)</li>
            <li>Thưởng lễ, Tết, thưởng năm theo quy định</li>
            <li>Đào tạo nâng bậc thợ miễn phí, cấp chứng chỉ nghề</li>
        </ul>
        
        <div class="alert alert-success mt-4">
            <strong>Liên hệ ứng tuyển:</strong><br>
            📧 Email: <a href="mailto:hr@mtechjsc.vn">hr@mtechjsc.vn</a><br>
            📞 Điện thoại: <strong>0909-123-456</strong> (Ms. Hương - Nhân sự)<br>
            🏢 Địa chỉ: Lô A5, KCN Biên Hòa 2, TP. Biên Hòa, Đồng Nai<br>
            ⏰ Giờ nhận hồ sơ: 8h00 - 16h30 (Thứ 2 - Thứ 7)
        </div>
    </div>',
    'Tuyển Dụng Công Nhân Hàn - Thu Nhập 12-18 Triệu - MTECH',
    'Tuyển 10 công nhân hàn MIG/TIG, lương 12-18 triệu, có chỗ ở, đào tạo nâng bậc. Liên hệ ngay!',
    6
),
(
    (SELECT id FROM blogs WHERE slug = 'tuyen-dung-quan-ly-du-an-cong-nghiep' LIMIT 1),
    '<div class="blog-text">
        <p>Để đáp ứng chiến lược mở rộng kinh doanh năm 2024, <strong>MTECH JSC</strong> cần bổ sung vị trí <strong>Quản Lý Dự Án Công Nghiệp</strong> để điều phối và giám sát các dự án gia công cơ khí lớn cho khách hàng trong và ngoài nước.</p>
        
        <h3 class="f_600 title_color mt-4">Tổng quan vị trí</h3>
        <p>Vị trí này báo cáo trực tiếp cho Giám đốc Vận hành, chịu trách nhiệm quản lý toàn bộ vòng đời dự án từ tiếp nhận yêu cầu đến bàn giao sản phẩm.</p>
        
        <h3 class="f_600 title_color mt-4">Trách nhiệm chính</h3>
        <ul>
            <li>Lập kế hoạch dự án: timeline, nguồn lực, ngân sách</li>
            <li>Phối hợp các phòng ban (Kỹ thuật, Sản xuất, QC, Logistics) để đảm bảo tiến độ</li>
            <li>Giám sát tiến độ sản xuất, xử lý rủi ro và phát sinh</li>
            <li>Đối thoại trực tiếp với khách hàng, báo cáo tiến độ định kỳ</li>
            <li>Kiểm soát chi phí dự án, đảm bảo lợi nhuận mục tiêu</li>
            <li>Tổ chức họp review sau dự án, rút kinh nghiệm cải tiến</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Yêu cầu công việc</h3>
        <ul>
            <li>Tốt nghiệp Đại học chuyên ngành Quản lý Dự án, Cơ khí, Công nghiệp</li>
            <li>Tối thiểu 3 năm kinh nghiệm quản lý dự án trong ngành chế tạo/cơ khí</li>
            <li>Có chứng chỉ PMP hoặc tương đương là lợi thế lớn</li>
            <li>Kỹ năng lãnh đạo, giao tiếp và đàm phán xuất sắc</li>
            <li>Thành thạo tiếng Anh (giao tiếp và viết)</li>
            <li>Sử dụng tốt MS Project hoặc các công cụ quản lý dự án tương tự</li>
            <li>Chịu được áp lực cao, linh hoạt trong môi trường đa dạng</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Phúc lợi hấp dẫn</h3>
        <ul>
            <li>Lương cứng: <strong>25-40 triệu/tháng</strong></li>
            <li>Thưởng theo doanh thu dự án: từ 2-6 tháng lương/năm</li>
            <li>Xe đưa đón từ TP.HCM và Biên Hòa</li>
            <li>Chế độ nghỉ phép 15 ngày/năm + ngày nghỉ lễ theo luật</li>
            <li>Bảo hiểm sức khỏe cao cấp cho bản thân và người thân</li>
            <li>Đào tạo quản lý chuyên sâu, cơ hội thăng tiến lên vị trí Giám đốc Dự án</li>
            <li>Tham gia các khóa học PMP/Agile miễn phí</li>
        </ul>
        
        <h3 class="f_600 title_color mt-4">Tại sao chọn MTECH?</h3>
        <p>MTECH là đối tác tin cậy của nhiều tập đoàn đa quốc gia như Siemens, ABB, Samsung. Bạn sẽ có cơ hội làm việc với các dự án công nghệ cao, tiếp xúc với tiêu chuẩn quốc tế và phát triển sự nghiệp lâu dài.</p>
        
        <div class="alert alert-primary mt-4">
            <strong>Gửi hồ sơ ngay:</strong><br>
            📧 <a href="mailto:hr@mtechjsc.vn">hr@mtechjsc.vn</a> (tiêu đề: [QLDA] - Họ tên)<br>
            📞 <strong>0909-123-456</strong> (Mr. Thành - Giám đốc Nhân sự)<br>
            🌐 Website: <a href="https://mtechjsc.vn">mtechjsc.vn</a>
        </div>
    </div>',
    'Tuyển Dụng Quản Lý Dự Án - Lương 25-40 Triệu - MTECH JSC',
    'Tuyển Quản lý Dự án Công nghiệp, lương 25-40 triệu, thưởng hấp dẫn. Yêu cầu 3 năm kinh nghiệm. Ứng tuyển ngay!',
    10
);

-- ------------------------------------------------------------
-- 5. Gán tags cho các bài tuyển dụng
-- Giả sử các blog vừa thêm có ID là 5, 6, 7 và tags mới có ID 7, 8, 9
-- ------------------------------------------------------------
-- Lấy blog_id và tag_id động để tránh lỗi nếu ID không khớp
INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-ky-su-co-khi-mtech' AND t.slug = 'tuyen-dung';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-ky-su-co-khi-mtech' AND t.slug = 'ky-su';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-cong-nhan-han-chuyen-nghiep' AND t.slug = 'tuyen-dung';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-cong-nhan-han-chuyen-nghiep' AND t.slug = 'co-hoi-nghe-nghiep';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-quan-ly-du-an-cong-nghiep' AND t.slug = 'tuyen-dung';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-quan-ly-du-an-cong-nghiep' AND t.slug = 'co-hoi-nghe-nghiep';

INSERT INTO `blog_tag_map` (`blog_id`, `tag_id`)
SELECT b.id, t.id 
FROM blogs b, blog_tags t 
WHERE b.slug = 'tuyen-dung-quan-ly-du-an-cong-nghiep' AND t.slug = 'ky-su';
