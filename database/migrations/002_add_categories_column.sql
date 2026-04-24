-- ============================================================
-- Migration: 002_add_categories_column.sql
-- Description: Thêm các cột chi tiết cho trang categories detail
--              Dựa trên cấu trúc giao diện service_details_area
-- ============================================================

-- Ảnh gallery chính (2 ảnh hiển thị song song ở đầu trang detail)
ALTER TABLE `categories`
    ADD COLUMN `image_1`               VARCHAR(500) DEFAULT NULL COMMENT 'Ảnh gallery 1 (w_55)' AFTER `image`,
    ADD COLUMN `image_2`               VARCHAR(500) DEFAULT NULL COMMENT 'Ảnh gallery 2 (w_45)' AFTER `image_1`,

    -- Mô tả chi tiết dài (phần service_content)
    ADD COLUMN `detail_description`    TEXT         DEFAULT NULL COMMENT 'Mô tả chi tiết đầy đủ hiển thị trong trang detail' AFTER `description`,

    -- Benefit section (media benefit_service - ảnh trái, text phải)
    ADD COLUMN `benefit_image`         VARCHAR(500) DEFAULT NULL COMMENT 'Ảnh minh họa phần Benefit of Service' AFTER `detail_description`,
    ADD COLUMN `benefit_title`         VARCHAR(255) DEFAULT NULL COMMENT 'Tiêu đề phần Benefit of Service' AFTER `benefit_image`,
    ADD COLUMN `benefit_description`   TEXT         DEFAULT NULL COMMENT 'Mô tả phần Benefit of Service' AFTER `benefit_title`,
    ADD COLUMN `benefit_items`         JSON         DEFAULT NULL COMMENT 'Danh sách bullet points của Benefit (JSON array of strings)' AFTER `benefit_description`,

    -- Feature section (2 service items với icon + title + text)
    ADD COLUMN `feature_image`         VARCHAR(500) DEFAULT NULL COMMENT 'Ảnh minh họa phần Features' AFTER `benefit_items`,
    ADD COLUMN `feature_1_icon`        VARCHAR(100) DEFAULT NULL COMMENT 'Class icon feature 1 (flaticon-clock, v.v.)' AFTER `feature_image`,
    ADD COLUMN `feature_1_title`       VARCHAR(255) DEFAULT NULL COMMENT 'Tiêu đề feature 1' AFTER `feature_1_icon`,
    ADD COLUMN `feature_1_text`        TEXT         DEFAULT NULL COMMENT 'Mô tả feature 1' AFTER `feature_1_title`,
    ADD COLUMN `feature_2_icon`        VARCHAR(100) DEFAULT NULL COMMENT 'Class icon feature 2' AFTER `feature_1_text`,
    ADD COLUMN `feature_2_title`       VARCHAR(255) DEFAULT NULL COMMENT 'Tiêu đề feature 2' AFTER `feature_2_icon`,
    ADD COLUMN `feature_2_text`        TEXT         DEFAULT NULL COMMENT 'Mô tả feature 2' AFTER `feature_2_title`,

    -- FAQ accordion (More information section)
    ADD COLUMN `faq_items`             JSON         DEFAULT NULL COMMENT 'Danh sách FAQ accordion (JSON array: [{question, answer}])' AFTER `feature_2_text`;

-- ============================================================
-- Seed data chi tiết cho tất cả 6 categories
-- Ảnh dùng chung link từ template HTML mẫu (agricultural-processing)
-- ============================================================

-- 1. Agricultural Processing
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Completely synergize resource taxing relationships via premier niche markets. Professionally cultivate one-to-one customer service with robust ideas. Dynamically innovate resource-leveling customer service for state of the art customer service.\n\nObjectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top-line web services vis-a-vis cutting-edge deliverables.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Phosfluorescently engage worldwide methodologies with web-enabled technology. Interactively coordinate proactive e-commerce via process-centric.',
    `benefit_items`       = '["Completely synergize resource taxing","Relationships via premier niche markets.","Professionally cultivate one-to-one customer","Dynamically innovate resource-leveling customer."]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = '#1 Manufacturing Unit',
    `feature_1_text`      = 'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Advanced Processing Technology',
    `feature_2_text`      = 'Proactively envisioned multimedia based expertise and cross-media growth strategies. Seamlessly visualize quality intellectual capital.',
    `faq_items`           = '[{"question":"Override the digital divide with additional clickthroughs information highway.","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Seamlessly visualize quality intellectual capital without superior.","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Proactively envisioned multimedia based expertise and cross growth strategies.","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Objectively innovate empowered manufactured products whereas parallel platform.","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'agricultural-processing';

-- 2. Chemical Industry
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Leverage agile frameworks to provide a robust synopsis for high level overviews in the chemical industry. Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition.\n\nOrganically grow the holistic world view of disruptive innovation via workplace diversity and empowerment. Bring to the table win-win survival strategies to ensure proactive domination of chemical processing solutions.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Capitalize on low hanging fruit to identify a ballpark value added activity to beta test chemical processes and safety protocols.',
    `benefit_items`       = '["Advanced safety and compliance standards","Cutting-edge chemical synthesis processes","Sustainable and eco-friendly solutions","24/7 technical support and monitoring"]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = 'Safety First Approach',
    `feature_1_text`      = 'Nanotechnology immersion along the information highway will close the loop on focusing solely on the bottom line of chemical safety.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Precision Chemical Control',
    `feature_2_text`      = 'Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway.',
    `faq_items`           = '[{"question":"What safety standards do you follow in chemical processing?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"How do you ensure environmental compliance?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"What types of chemical processes do you specialize in?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Do you provide on-site chemical engineering consultation?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'chemical-industry';

-- 3. Civil Engineering
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Bring to the table win-win survival strategies to ensure proactive domination in civil engineering and infrastructure development. At the end of the day, going forward, a new normal that has evolved from generation X is on the runway heading towards a streamlined cloud solution.\n\nCapitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway will close the loop.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Podcasting operational change management inside of workflows to establish a framework for civil engineering excellence.',
    `benefit_items`       = '["Comprehensive structural design and analysis","Urban planning and infrastructure development","Cost-effective construction management","Sustainable building practices and materials"]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = 'On-Time Project Delivery',
    `feature_1_text`      = 'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Structural Engineering Excellence',
    `feature_2_text`      = 'Proactively envisioned multimedia based expertise and cross-media growth strategies. Seamlessly visualize quality intellectual capital.',
    `faq_items`           = '[{"question":"What types of civil engineering projects do you handle?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"How do you manage large-scale infrastructure projects?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Do you offer project management alongside engineering services?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"What sustainable construction methods do you employ?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'civil-engineering';

-- 4. Energy & Power
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Dynamically innovate resource-leveling customer service for state of the art energy solutions. Objectively innovate empowered manufactured products whereas parallel platforms in the power sector.\n\nHolisticly predominate extensible testing procedures for reliable energy supply chains. Dramatically engage top-line web services vis-a-vis cutting-edge power generation deliverables. Proactively envisioned multimedia based expertise and cross-media growth strategies.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Interactively coordinate proactive e-commerce via process-centric energy management and renewable power solutions.',
    `benefit_items`       = '["Renewable energy integration and management","Smart grid technology and optimization","Energy efficiency audits and consulting","24/7 power system monitoring and support"]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = 'Reliable Power Solutions',
    `feature_1_text`      = 'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Smart Energy Management',
    `feature_2_text`      = 'Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway will close the loop.',
    `faq_items`           = '[{"question":"What renewable energy solutions do you provide?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"How do you optimize energy consumption for industrial clients?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Do you offer grid integration services for solar and wind power?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"What is your approach to power system reliability and redundancy?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'energy-power';

-- 5. Mechanical Engineering
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Professionally cultivate one-to-one customer service with robust mechanical engineering ideas. Dynamically innovate resource-leveling customer service for state of the art manufacturing solutions.\n\nSeamlessly visualize quality intellectual capital without superior collaboration and idea-sharing in mechanical design. Completely synergize resource taxing relationships via premier niche markets in precision engineering.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Phosfluorescently engage worldwide methodologies with web-enabled technology for mechanical design and manufacturing excellence.',
    `benefit_items`       = '["Precision component design and manufacturing","Advanced CAD/CAM engineering capabilities","Preventive maintenance and reliability programs","Custom mechanical solutions for any industry"]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = 'Precision Manufacturing',
    `feature_1_text`      = 'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Advanced Design Engineering',
    `feature_2_text`      = 'Proactively envisioned multimedia based expertise and cross-media growth strategies. Seamlessly visualize quality intellectual capital without superior.',
    `faq_items`           = '[{"question":"What mechanical engineering design services do you offer?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"How do you handle custom manufacturing requirements?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Do you provide maintenance and repair services for mechanical systems?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"What industries do your mechanical engineering services cover?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'mechanical-engineering';

-- 6. Oil & Gas Engineering
UPDATE `categories` SET
    `image_1`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_1.jpg',
    `image_2`             = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_2.jpg',
    `detail_description`  = 'Objectively innovate empowered manufactured products whereas parallel platforms in the oil and gas sector. Holisticly predominate extensible testing procedures for reliable upstream and downstream supply chains.\n\nDramatically engage top-line web services vis-a-vis cutting-edge oil and gas deliverables. Proactively envisioned multimedia based expertise and cross-media growth strategies for energy exploration and extraction.',
    `benefit_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_3.jpg',
    `benefit_title`       = 'Benefit of Service',
    `benefit_description` = 'Capitalize on low hanging fruit to identify a ballpark value added activity to beta test oil and gas engineering processes.',
    `benefit_items`       = '["Upstream exploration and drilling expertise","Pipeline design and integrity management","Refinery process optimization services","HSE compliance and risk management"]',
    `feature_image`       = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/ag_4.jpg',
    `feature_1_icon`      = 'flaticon-clock',
    `feature_1_title`     = 'Upstream & Downstream Expertise',
    `feature_1_text`      = 'Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.',
    `feature_2_icon`      = 'flaticon-gear',
    `feature_2_title`     = 'Pipeline & Refinery Solutions',
    `feature_2_text`      = 'Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway will close the loop.',
    `faq_items`           = '[{"question":"What oil and gas engineering services do you specialize in?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"How do you manage HSE compliance on oil and gas projects?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"Do you provide pipeline inspection and integrity services?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."},{"question":"What is your experience with offshore oil and gas projects?","answer":"Objectively innovate empowered manufactured products whereas parallel platforms. Holisticly predominate extensible testing procedures for reliable supply chains. Dramatically engage top web services vis-a-vis cutting-edge deliverables."}]'
WHERE `slug` = 'oil-gas-engineering';
