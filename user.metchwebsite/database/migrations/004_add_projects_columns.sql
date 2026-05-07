-- ==========================================
-- Migration: 004_add_projects_columns.sql
-- Description: Thêm cột cho project detail page
-- ==========================================

-- Thêm các cột mới cho bảng projects
ALTER TABLE `projects`
    -- Ảnh chi tiết chính (khác với ảnh thumbnail)
    ADD COLUMN `detail_image` VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh chi tiết chính' AFTER `image`,
    
    -- Nhãn trạng thái hiển thị (Completed, In Progress, On Hold...)
    ADD COLUMN `status_label` VARCHAR(50) NULL DEFAULT 'Completed' COMMENT 'Nhãn trạng thái hiển thị' AFTER `status`,
    
    -- URL live demo
    ADD COLUMN `live_demo` VARCHAR(255) NULL COMMENT 'URL live demo' AFTER `status_label`,
    
    -- Tags (phân cách bằng dấu phẩy)
    ADD COLUMN `tags` VARCHAR(255) NULL COMMENT 'Tags phân cách bằng dấu phẩy' AFTER `live_demo`,
    
    -- Section "What we did"
    ADD COLUMN `what_we_did_title` VARCHAR(255) NULL DEFAULT 'What we did' COMMENT 'Tiêu đề section What we did' AFTER `tags`,
    ADD COLUMN `what_we_did` TEXT NULL COMMENT 'Nội dung HTML section What we did' AFTER `what_we_did_title`,
    ADD COLUMN `what_we_did_image` VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh section What we did' AFTER `what_we_did`,
    
    -- Section "Results"
    ADD COLUMN `results_title` VARCHAR(255) NULL DEFAULT 'Results' COMMENT 'Tiêu đề section Results' AFTER `what_we_did_image`,
    ADD COLUMN `results` TEXT NULL COMMENT 'Nội dung text section Results' AFTER `results_title`,
    ADD COLUMN `result_items` TEXT NULL COMMENT 'JSON array các result items' AFTER `results`;

-- Cập nhật dữ liệu mẫu cho project đầu tiên
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.demolive.com',
    `tags` = 'industrial, welding, chemical',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum like Aldus PageMaker including.</p>',
    `result_items` = '["The release of Letraset sheets containing", "Publishing software like Aldus", "Release of Letraset sheets containing", "Publishing software like Aldus"]'
WHERE `id` = 1;

-- Cập nhật dữ liệu mẫu cho project id = 2 (Welding Processing)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.welding-demo.com',
    `tags` = 'welding, metalwork, industrial',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We implemented a comprehensive welding processing system that meets international standards for quality and safety. Our team utilized advanced MIG and TIG welding techniques to ensure precision and durability in all welded joints.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Advanced Welding Technology</h5><p>Implemented state-of-the-art automated welding systems with real-time quality monitoring and control systems.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Quality Assurance Protocols</h5><p>Established rigorous testing procedures including X-ray inspection and ultrasonic testing for weld integrity.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The welding processing project achieved exceptional results with zero defects in over 10,000 welds. Our quality control measures exceeded industry standards, resulting in a 40% reduction in rework requirements.</p><p>Client satisfaction reached 98% with significant improvements in production efficiency and structural integrity of all welded components.</p>',
    `result_items` = '["Zero defect rate in 10,000+ welds", "40% reduction in rework", "98% client satisfaction", "Full ISO 3834 compliance"]'
WHERE `id` = 2;

-- Cập nhật dữ liệu mẫu cho project id = 3 (Railway Project)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'In Progress',
    `live_demo` = 'www.railway-demo.com',
    `tags` = 'railway, infrastructure, transport',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We are developing a modern railway infrastructure system designed to handle high-speed trains and heavy freight loads. The project involves track laying, signaling systems, and station construction.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Track Engineering</h5><p>Designed and installed heavy-duty railway tracks capable of supporting 30-ton axle loads with enhanced stability.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Signaling Systems</h5><p>Implemented advanced electronic signaling and communication systems for improved train safety and scheduling.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Current Progress',
    `results` = '<p>The railway project is currently 75% complete with major track sections already operational. Initial testing shows the infrastructure can safely support trains at speeds up to 160 km/h.</p><p>Upon completion, this railway will connect three major industrial zones and reduce transport time by 60%.</p>',
    `result_items` = '["75% project completion", "160 km/h test speeds achieved", "3 industrial zones connected", "60% transport time reduction"]'
WHERE `id` = 3;

-- Cập nhật dữ liệu mẫu cho project id = 4 (Material Engineering)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.material-demo.com',
    `tags` = 'materials, engineering, construction',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We conducted comprehensive material engineering research and development to create high-performance construction materials. Our focus was on durability, sustainability, and cost-effectiveness.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Composite Material Development</h5><p>Developed fiber-reinforced polymer composites with 3x the strength-to-weight ratio of traditional materials.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Sustainable Solutions</h5><p>Created eco-friendly building materials using recycled components without compromising structural integrity.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The material engineering project successfully developed 5 new composite materials now patented and in commercial production. These materials have been adopted by major construction firms across the region.</p><p>Our sustainable material line has diverted over 500 tons of waste from landfills while providing superior performance characteristics.</p>',
    `result_items` = '["5 new materials patented", "3x strength-to-weight improvement", "500+ tons waste diverted", "Regional industry adoption"]'
WHERE `id` = 4;

-- Cập nhật dữ liệu mẫu cho project id = 5 (Wind Power Project)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.windpower-demo.com',
    `tags` = 'wind, energy, renewable, power',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We designed and installed a wind farm consisting of 12 large-scale turbines with a combined capacity of 36 MW. The project included foundation work, tower installation, and electrical grid integration.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Turbine Installation</h5><p>Installed 12 state-of-the-art wind turbines with 120m rotor diameter and advanced pitch control systems.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Grid Integration</h5><p>Built comprehensive electrical infrastructure including substations and transmission lines to connect to the national grid.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The wind power project generates approximately 85,000 MWh annually, providing clean energy to over 20,000 households. Carbon emissions have been reduced by an estimated 45,000 tons per year.</p><p>The project achieved ROI ahead of schedule and has become a model for renewable energy development in the region.</p>',
    `result_items` = '["36 MW installed capacity", "85,000 MWh annual generation", "45,000 tons CO2 reduction", "20,000+ households powered"]'
WHERE `id` = 5;

-- Cập nhật dữ liệu mẫu cho project id = 6 (Robot Engineering)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.robot-demo.com',
    `tags` = 'robot, automation, engineering, AI',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We developed an industrial robotics system for automated manufacturing processes. The system includes robotic arms, conveyor integration, and AI-powered quality control systems.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Robotic System Design</h5><p>Designed and programmed 6-axis robotic arms with precision control capable of handling complex assembly operations.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">AI Integration</h5><p>Implemented machine vision and AI algorithms for real-time defect detection and process optimization.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The robotics engineering project increased production efficiency by 250% while reducing defect rates to less than 0.1%. The system operates 24/7 with minimal downtime for maintenance.</p><p>The AI quality control system has achieved 99.7% accuracy in defect detection, surpassing human inspection capabilities.</p>',
    `result_items` = '["250% efficiency increase", "99.7% AI accuracy achieved", "0.1% defect rate", "24/7 continuous operation"]'
WHERE `id` = 6;

-- Cập nhật dữ liệu mẫu cho project id = 7 (Flyover Bridge)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.bridge-demo.com',
    `tags` = 'bridge, construction, infrastructure',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We constructed a major flyover bridge to alleviate traffic congestion at a critical urban intersection. The project involved complex foundation work, structural steel installation, and road surface construction.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Structural Engineering</h5><p>Designed a 450-meter elevated structure capable of supporting heavy vehicle loads with earthquake resistance.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Traffic Management</h5><p>Implemented phased construction to minimize disruption to existing traffic flows during the building process.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The flyover bridge project was completed 2 months ahead of schedule and has reduced traffic congestion by 70% at the intersection. The structure meets all international safety standards.</p><p>Commuter travel time through the area has decreased by an average of 15 minutes during peak hours.</p>',
    `result_items` = '["450m bridge completed", "2 months ahead of schedule", "70% congestion reduction", "15 min travel time saved"]'
WHERE `id` = 7;

-- Cập nhật dữ liệu mẫu cho project id = 8 (Welding Processing II)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'On Hold',
    `live_demo` = 'www.welding2-demo.com',
    `tags` = 'welding, iron, metalwork, sector',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>Phase 2 of our welding processing expansion involves upgrading existing facilities with plasma cutting technology and robotic welding stations for the iron sector.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Plasma Cutting Systems</h5><p>Installing CNC plasma cutting machines capable of processing iron plates up to 50mm thickness with precision.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Facility Upgrade</h5><p>Renovating workshop layout and installing new ventilation and safety systems for improved working conditions.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Current Status',
    `results` = '<p>The Welding Processing II project is temporarily on hold pending additional funding approval. Initial equipment procurement is complete and site preparation is 40% finished.</p><p>Once resumed, the project will double our iron sector processing capacity and reduce turnaround times by 30%.</p>',
    `result_items` = '["40% site preparation complete", "Equipment procurement done", "2x capacity increase planned", "30% faster turnaround"]'
WHERE `id` = 8;

-- Cập nhật dữ liệu mẫu cho project id = 9 (Petroleum Chamber)
UPDATE `projects` SET 
    `detail_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_1.jpg',
    `status_label` = 'Completed',
    `live_demo` = 'www.petroleum-demo.com',
    `tags` = 'petroleum, oil, gas, storage',
    `what_we_did_title` = 'What we did',
    `what_we_did` = '<p>We constructed a large-scale petroleum storage chamber facility with multiple tanks and comprehensive safety systems. The project included excavation, tank installation, and pipeline connections.</p><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Storage Tank Installation</h5><p>Built 6 storage tanks with total capacity of 50,000 cubic meters using double-wall construction for leak prevention.</p></div><div class="pr_item"><span class="dot"></span><h5 class="f_size_18 f_600 title_color">Safety Systems</h5><p>Installed fire suppression, leak detection, and emergency shutdown systems meeting API and NFPA standards.</p></div>',
    `what_we_did_image` = 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/project_details_2.jpg',
    `results_title` = 'Results',
    `results` = '<p>The petroleum chamber project achieved full regulatory compliance and passed all safety inspections on the first attempt. The facility has been operational for 18 months without any incidents.</p><p>The storage capacity supports regional fuel distribution needs and provides strategic reserves equivalent to 30 days of regional consumption.</p>',
    `result_items` = '["50,000 cubic meter capacity", "6 double-wall tanks installed", "100% regulatory compliance", "18 months incident-free"]'
WHERE `id` = 9;
