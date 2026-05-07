-- ============================================================
-- Migration: 023_update_blogs_image.sql
-- Description: Update ảnh blog_single.jpg cho các bài viết blog
-- ============================================================

-- Update ảnh cho tất cả các bài viết blog
UPDATE `blogs` 
SET `image` = 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/blog_single.jpg'
WHERE `id` IN (1, 2, 3, 4, 5);
