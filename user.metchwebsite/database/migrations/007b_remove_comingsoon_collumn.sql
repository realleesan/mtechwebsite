-- Migration: Remove unnecessary columns from comingsoon_settings
-- Date: 2026-04-26
-- Description: Keep only is_active and target_date, remove all text/content columns
-- as they are now static in the template
-- 
-- IMPORTANT: Run each ALTER TABLE statement one by one in phpMyAdmin
-- If a column doesn't exist, skip that line

-- Drop title column
ALTER TABLE comingsoon_settings DROP COLUMN title;

-- Drop description column  
ALTER TABLE comingsoon_settings DROP COLUMN description;

-- Drop subscribe_text column
ALTER TABLE comingsoon_settings DROP COLUMN subscribe_text;

-- Drop email_placeholder column
ALTER TABLE comingsoon_settings DROP COLUMN email_placeholder;

-- Drop button_text column
ALTER TABLE comingsoon_settings DROP COLUMN button_text;

-- Drop background_color column
ALTER TABLE comingsoon_settings DROP COLUMN background_color;

-- Drop text_color column
ALTER TABLE comingsoon_settings DROP COLUMN text_color;

-- Note: The coming soon page now uses static content from the template:
-- - Title: "We're Coming Soon..."
-- - Description: "Our website is under construction. We'll be here soon with our new awesome site,"
-- - Subscribe text: "Subscribe to be notified."
-- - Email placeholder: "Enter your email address"
-- - Button text: "Subscribe now"
-- - Background: Yellow gradient (#ffc107 -> #ff8f00)
-- - Text color: #242424
-- - Accent color: #ffb600

-- Only is_active (toggle on/off) and target_date (countdown) remain dynamic
SELECT 'Migration 007b completed: Removed unnecessary columns from comingsoon_settings' AS result;
