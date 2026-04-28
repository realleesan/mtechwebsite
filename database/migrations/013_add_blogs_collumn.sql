-- Add show_in_menu column to blog_categories table
-- This column controls whether a blog category should appear in the header dropdown menu
-- 1 = show in menu, 0 = hide from menu

ALTER TABLE `blog_categories` ADD COLUMN `show_in_menu` TINYINT(1) DEFAULT 0 AFTER `status`;

-- Create index for better query performance
CREATE INDEX idx_blog_categories_show_in_menu ON `blog_categories`(`show_in_menu`, `status`);
