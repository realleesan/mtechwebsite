-- ============================================================
-- Migration: 001b_update_categories_name_vi.sql
-- Description: Cập nhật tên và slug các dịch vụ sang tiếng Việt
--              theo đúng nghiệp vụ của MTECH
-- Depends on:  001_create_categories_table.sql
-- ============================================================

UPDATE `categories`
SET
    `name` = 'Lập quy hoạch xây dựng và Tư vấn dự án đầu tư',
    `slug` = 'lap-quy-hoach-xay-dung-va-tu-van-du-an-dau-tu'
WHERE `slug` = 'agricultural-processing';

UPDATE `categories`
SET
    `name` = 'Thiết kế xây dựng chuyên dụng',
    `slug` = 'thiet-ke-xay-dung-chuyen-dung'
WHERE `slug` = 'chemical-industry';

UPDATE `categories`
SET
    `name` = 'Quản lý dự án, Giám sát thi công và Kiểm định chất lượng',
    `slug` = 'quan-ly-du-an-giam-sat-thi-cong-kiem-dinh'
WHERE `slug` = 'civil-engineering';

UPDATE `categories`
SET
    `name` = 'Quản lý chi phí xây dựng và Tư vấn đấu thầu',
    `slug` = 'quan-ly-chi-phi-xay-dung-tu-van-dau-thau'
WHERE `slug` = 'energy-power';

UPDATE `categories`
SET
    `name` = 'Tư vấn kỹ thuật tối ưu hóa năng lượng',
    `slug` = 'tu-van-ky-thuat-toi-uu-hoa-nang-luong'
WHERE `slug` = 'mechanical-engineering';

UPDATE `categories`
SET
    `name` = 'Tổng thầu tư vấn dự án đầu tư',
    `slug` = 'tong-thau-tu-van-du-an-dau-tu'
WHERE `slug` = 'oil-gas-engineering';
