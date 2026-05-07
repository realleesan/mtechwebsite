-- Migration 019: Add subject column to contacts table
-- Run this against your database before deploying the updated ContactsModel

ALTER TABLE `contacts`
    ADD COLUMN `subject` VARCHAR(255) NULL DEFAULT NULL
    AFTER `phone`;
