-- =========================================================================================
-- FILE: 024_update_categories_detail_content.sql
-- MỤC ĐÍCH: Cập nhật nội dung chi tiết các dịch vụ dựa trên HSNL MTECH 2026.pdf
-- NGÀY TẠO: 2026-05-05
-- =========================================================================================

-- -----------------------------------------------------------------------------------------
-- 1. Lập quy hoạch xây dựng và Tư vấn dự án đầu tư
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'MTECH cung cấp dịch vụ toàn diện trong công tác chuẩn bị đầu tư bao gồm: Lập quy hoạch xây dựng; Lập hồ sơ báo cáo nghiên cứu khả thi và thiết kế cơ sở; Lập, thẩm tra dự án đầu tư và đánh giá hiệu quả dự án đầu tư xây dựng công trình. Chúng tôi đồng hành cùng sự phát triển bền vững của doanh nghiệp.',
    `benefit_title`       = 'Năng lực Lập quy hoạch và Dự án',
    `benefit_description` = 'MTECH có kinh nghiệm triển khai lập quy hoạch cho nhiều dự án quy mô cực lớn lên tới hàng ngàn hecta, đảm bảo pháp lý và đánh giá hiệu quả đầu tư chính xác.',
    `benefit_items`       = '["Lập quy hoạch các khu công nghiệp, nông nghiệp CNC", "Lập hồ sơ báo cáo NCKT và thiết kế cơ sở", "Thẩm tra dự án đầu tư và tổng mức đầu tư", "Đánh giá hiệu quả dự án đầu tư xây dựng"]',
    `feature_1_title`     = 'Dự án Nông nghiệp quy mô lớn',
    `feature_1_text`      = 'Thực hiện quy hoạch Khu nông nghiệp ứng dụng CNC Xuân Thiện Cư M''gar (3.417 ha), Tổ hợp chăn nuôi CNC Thuận Lợi (1.500 ha).',
    `feature_2_title`     = 'Dự án Công nghiệp & VLXD',
    `feature_2_text`      = 'Lập quy hoạch và NCKT Nhà máy xi măng Hà Tiên Kiên Lương (60ha), Nhà máy Thép Xanh Xuân Thiện Nam Định công suất 7.5 triệu tấn/năm.',
    `faq_items`           = '[{"question": "Công ty có kinh nghiệm lập quy hoạch những lĩnh vực nào?", "answer": "MTECH có bề dày kinh nghiệm lập quy hoạch trong các lĩnh vực: Vật liệu xây dựng (xi măng), Luyện kim, Năng lượng và Nông nghiệp công nghệ cao."}]'
WHERE `slug` = 'lap-quy-hoach-xay-dung-va-tu-van-du-an-dau-tu';

-- -----------------------------------------------------------------------------------------
-- 2. Thiết kế xây dựng chuyên dụng
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'MTECH tự hào đạt Chứng chỉ năng lực hoạt động xây dựng Hạng I đối với Thiết kế, thẩm tra thiết kế xây dựng Công trình Nhà công nghiệp và Công trình sản xuất VLXD. Chúng tôi chuyên thiết kế kiến trúc, kết cấu công trình dân dụng - công nghiệp, hạ tầng kỹ thuật và lắp đặt dây chuyền công nghệ silicat.',
    `benefit_title`       = 'Chứng chỉ Năng lực Hạng I',
    `benefit_description` = 'Đủ điều kiện thiết kế, thẩm tra thiết kế các công trình công nghiệp cấp I, cấp II, đáp ứng tiêu chuẩn khắt khe nhất của các Tập đoàn lớn.',
    `benefit_items`       = '["Thiết kế, thẩm tra công trình Nhà công nghiệp Hạng I", "Thiết kế, thẩm tra công trình VLXD Hạng I", "Thiết kế công trình năng lượng, khai thác mỏ Hạng II", "Thiết kế hệ thống điện - tự động hóa"]',
    `feature_1_title`     = 'Dự án Công nghiệp VLXD',
    `feature_1_text`      = 'Thiết kế Nhà máy xi măng Xuân Thành (Line 3 - 5 triệu tấn/năm), Nhà máy xi măng Long Sơn, xi măng Thành Thắng.',
    `feature_2_title`     = 'Công nghiệp Nặng & Luyện kim',
    `feature_2_text`      = 'Thiết kế Nhà máy Thép Xanh Xuân Thiện Nam Định (7,5 triệu tấn/năm), Nhà máy luyện cán thép DST Nghi Sơn (1,2 triệu tấn/năm).',
    `faq_items`           = '[{"question": "MTECH có thiết kế các hạng mục năng lượng không?", "answer": "Có. MTECH sở hữu Chứng chỉ năng lực thiết kế công trình năng lượng Hạng II, chuyên thiết kế hệ thống trạm phát điện và đường dây."}]'
WHERE `slug` = 'thiet-ke-xay-dung-chuyen-dung';

-- -----------------------------------------------------------------------------------------
-- 3. Quản lý dự án, Giám sát thi công và Kiểm định chất lượng
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'Chúng tôi cung cấp dịch vụ quản lý dự án đầu tư xây dựng, giám sát thi công và kiểm định chất lượng công trình. Đội ngũ kỹ sư, thạc sĩ của MTECH giám sát chặt chẽ từ khâu xây dựng nền móng đến hoàn thiện và lắp đặt thiết bị cơ điện, đảm bảo công trình đi vào vận hành an toàn.',
    `benefit_title`       = 'Giám sát chặt chẽ, chuyên nghiệp',
    `benefit_description` = 'Đảm bảo tuyệt đối chất lượng, tiến độ thi công và an toàn lao động tại công trường nhờ quy trình quản lý chuyên nghiệp.',
    `benefit_items`       = '["Quản lý dự án đầu tư xây dựng Hạng II", "Giám sát thi công công trình công nghiệp Hạng I", "Giám sát lắp đặt thiết bị công trình Hạng II", "Kiểm định chất lượng kết cấu công trình"]',
    `feature_1_title`     = 'Giám sát Nhà máy xi măng',
    `feature_1_text`      = 'Thực hiện giám sát thi công xây dựng Nhà máy xi măng Xuân Thành Line 3, xi măng Long Sơn Line 3 & 4, xi măng Đại Dương.',
    `feature_2_title`     = 'Trạm phân phối & Trạm nghiền',
    `feature_2_text`      = 'Giám sát thi công các trạm phân phối xi măng Long Sơn tại Sóc Trăng, Ninh Thủy, Long An và Trạm nghiền Quảng Phúc.',
    `faq_items`           = '[{"question": "Lĩnh vực giám sát thế mạnh của MTECH là gì?", "answer": "Thế mạnh vượt trội của MTECH là giám sát thi công xây dựng và lắp đặt thiết bị dây chuyền công nghệ cho các nhà máy công nghiệp nặng và vật liệu xây dựng."}]'
WHERE `slug` = 'quan-ly-du-an-giam-sat-thi-cong-kiem-dinh';

-- -----------------------------------------------------------------------------------------
-- 4. Quản lý chi phí xây dựng và Tư vấn đấu thầu
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'Dịch vụ Quản lý chi phí và Tư vấn đấu thầu của MTECH giúp chủ đầu tư tối ưu hóa ngân sách. Chúng tôi thực hiện đo bóc khối lượng, xác định chỉ tiêu suất vốn đầu tư, đơn giá xây dựng, lập hồ sơ quyết toán và tư vấn lựa chọn nhà thầu minh bạch, hiệu quả.',
    `benefit_title`       = 'Tối ưu hóa chi phí đầu tư',
    `benefit_description` = 'Kiểm soát chặt chẽ ngân sách, hạn chế phát sinh và đảm bảo tính minh bạch, hiệu quả trong quá trình lựa chọn nhà thầu.',
    `benefit_items`       = '["Đo bóc khối lượng, lập và thẩm tra dự toán", "Xác định giá gói thầu, giá hợp đồng xây dựng", "Lập hồ sơ thanh toán, quyết toán vốn đầu tư", "Tư vấn đấu thầu chuyên nghiệp"]',
    `feature_1_title`     = 'Tư vấn đấu thầu dự án lớn',
    `feature_1_text`      = 'Đã tư vấn đấu thầu thành công cho các dự án quy mô như: Nhà máy xi măng Minh Tâm (5 triệu tấn/năm), xi măng Long Sơn Line 1.',
    `feature_2_title`     = 'Kiểm soát ngân sách chặt chẽ',
    `feature_2_text`      = 'Thực hiện định giá, đo bóc khối lượng cho các trạm nghiền, trạm phân phối lớn tại An Giang, Bến Tre, Hải Phòng.',
    `faq_items`           = '[{"question": "Dịch vụ quản lý chi phí bao gồm những hạng mục nào?", "answer": "Bao gồm lập/thẩm tra tổng mức đầu tư, dự toán, đo bóc khối lượng, kiểm soát chi phí và lập hồ sơ thanh quyết toán vốn đầu tư."}]'
WHERE `slug` = 'quan-ly-chi-phi-xay-dung-tu-van-dau-thau';

-- -----------------------------------------------------------------------------------------
-- 5. Tư vấn kỹ thuật tối ưu hóa năng lượng
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'Hướng tới xu hướng phát triển công nghiệp xanh, MTECH tư vấn các giải pháp kỹ thuật tối ưu hóa năng lượng. Chúng tôi tự hào là đơn vị tiên phong trong tư vấn, thiết kế hệ thống phát điện nhiệt dư (WHR) và cải tạo tháp trao đổi nhiệt cho các nhà máy công nghiệp nặng.',
    `benefit_title`       = 'Giải pháp Xanh - Bền vững',
    `benefit_description` = 'Tận dụng nhiệt thải từ lò nung để phát điện, giúp nhà máy tự chủ một phần điện năng, giảm chi phí vận hành và bảo vệ môi trường.',
    `benefit_items`       = '["Tư vấn, thiết kế Hệ thống phát điện nhiệt dư (WHR)", "Cải tạo tháp trao đổi nhiệt giảm tiêu hao than/điện", "Sử dụng rác thải, chất thải làm nhiên liệu thay thế"]',
    `feature_1_title`     = 'Hệ thống điện nhiệt dư (WHR)',
    `feature_1_text`      = 'Tư vấn và thiết kế WHR cho XM Nghi Sơn (18MW), XM Xuân Thành HN (18MW), XM Đồng Lâm (9MW), XM Sông Gianh.',
    `feature_2_title`     = 'Tối ưu hóa dây chuyền',
    `feature_2_text`      = 'Cải tạo tháp trao đổi nhiệt tại NM xi măng Chinfont, Vicem Hoàng Thạch, Vicem Bút Sơn giúp nâng cao hiệu suất đáng kể.',
    `faq_items`           = '[{"question": "Hệ thống phát điện nhiệt dư mang lại lợi ích gì?", "answer": "Hệ thống giúp tận dụng khí thải lò nung để phát điện, đáp ứng lên tới 20-30% nhu cầu điện năng của nhà máy xi măng, đồng thời giảm phát thải khí nhà kính."}]'
WHERE `slug` = 'tu-van-ky-thuat-toi-uu-hoa-nang-luong';

-- -----------------------------------------------------------------------------------------
-- 6. Tổng thầu tư vấn dự án đầu tư
-- -----------------------------------------------------------------------------------------
UPDATE `categories`
SET
    `detail_description`  = 'MTECH khẳng định vị thế khi đảm nhận vai trò Tổng thầu tư vấn cho hàng loạt đại dự án công nghiệp. Chúng tôi cung cấp gói giải pháp đồng bộ và bao quát toàn bộ vòng đời dự án: từ khảo sát địa hình, thiết kế cơ sở, thiết kế kỹ thuật đến quản lý dự án.',
    `benefit_title`       = 'Giải pháp tư vấn toàn diện',
    `benefit_description` = 'Khách hàng chỉ cần làm việc với một đầu mối duy nhất, đảm bảo tính nhất quán, đồng bộ, tiến độ và chất lượng cao nhất cho dự án.',
    `benefit_items`       = '["Tích hợp Khảo sát - Quy hoạch - Thiết kế", "Kiểm soát xuyên suốt quy trình vòng đời dự án", "Cam kết tiến độ và chất lượng theo chuẩn Quốc tế", "Đối tác chiến lược của Tập đoàn Xuân Thiện, Long Sơn"]',
    `feature_1_title`     = 'Tổng thầu Công nghiệp nặng',
    `feature_1_text`      = 'Đảm nhận tổng thầu tư vấn thiết kế cho Nhà máy xi măng Long Sơn Line 2, xi măng Thành Thắng, xi măng Thăng Long 2.',
    `feature_2_title`     = 'Tổng thầu Hệ thống Năng lượng',
    `feature_2_text`      = 'Tổng thầu tư vấn xây dựng hệ thống phát điện nhiệt dư cho NM xi măng Nghi Sơn (18MW), xi măng Quảng Phúc (10MW).',
    `faq_items`           = '[{"question": "Lợi ích khi chọn MTECH làm Tổng thầu tư vấn?", "answer": "Chủ đầu tư sẽ tiết kiệm được thời gian điều phối giữa các nhà thầu phụ, đảm bảo sự đồng bộ trong hồ sơ thiết kế và tối ưu hóa chi phí đầu tư tổng thể."}]'
WHERE `slug` = 'tong-thau-tu-van-du-an-dau-tu';

-- =========================================================================================
-- KẾT THÚC MIGRATION
-- =========================================================================================
