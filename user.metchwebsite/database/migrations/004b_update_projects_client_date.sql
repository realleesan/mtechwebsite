-- ==========================================
-- Migration: 005_update_projects_client_date.sql
-- Description: Cập nhật dữ liệu client và project_date cho các project
-- ==========================================

-- Cập nhật dữ liệu cho project id = 1 (Chemical Chamber)
UPDATE `projects` SET 
    `client` = 'Michale',
    `project_date` = '2018-08-22'
WHERE `id` = 1;

-- Cập nhật dữ liệu cho project id = 2 (Welding Processing)
UPDATE `projects` SET 
    `client` = 'Global Industries Ltd.',
    `project_date` = '2019-03-15'
WHERE `id` = 2;

-- Cập nhật dữ liệu cho project id = 3 (Railway Project)
UPDATE `projects` SET 
    `client` = 'National Transport Authority',
    `project_date` = '2020-06-10'
WHERE `id` = 3;

-- Cập nhật dữ liệu cho project id = 4 (Material Engineering)
UPDATE `projects` SET 
    `client` = 'BuildCorp Construction',
    `project_date` = '2019-11-05'
WHERE `id` = 4;

-- Cập nhật dữ liệu cho project id = 5 (Wind Power Project)
UPDATE `projects` SET 
    `client` = 'Green Energy Solutions',
    `project_date` = '2021-02-28'
WHERE `id` = 5;

-- Cập nhật dữ liệu cho project id = 6 (Robot Engineering)
UPDATE `projects` SET 
    `client` = 'AutoTech Manufacturing',
    `project_date` = '2020-09-18'
WHERE `id` = 6;

-- Cập nhật dữ liệu cho project id = 7 (Flyover Bridge)
UPDATE `projects` SET 
    `client` = 'City Infrastructure Dept.',
    `project_date` = '2018-12-12'
WHERE `id` = 7;

-- Cập nhật dữ liệu cho project id = 8 (Welding Processing II)
UPDATE `projects` SET 
    `client` = 'IronWorks Heavy Industry',
    `project_date` = '2022-01-20'
WHERE `id` = 8;

-- Cập nhật dữ liệu cho project id = 9 (Petroleum Chamber)
UPDATE `projects` SET 
    `client` = 'PetroStorage International',
    `project_date` = '2021-07-08'
WHERE `id` = 9;
