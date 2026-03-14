-- SQL Script to Activate All Content for Frontend Display
-- Run this script to ensure all content shows up on the frontend pages
-- All pages now use 100% database content with WHERE is_active=1 filter

-- Core Values Page
UPDATE core_values_hero SET is_active = 1;
UPDATE core_values_pillars SET is_active = 1;
UPDATE core_values_actions SET is_active = 1;

-- VVU Anthem Page
UPDATE anthem_hero SET is_active = 1;
UPDATE anthem_stanzas SET is_active = 1;
UPDATE anthem_video SET is_active = 1;
UPDATE anthem_about SET is_active = 1;

-- Ecology Page
UPDATE ecology_hero SET is_active = 1;
UPDATE ecology_philosophy SET is_active = 1;
UPDATE ecology_initiatives SET is_active = 1;
UPDATE ecology_stats SET is_active = 1;

-- The Campus Page
UPDATE campus_hero SET is_active = 1;
UPDATE campus_highlights SET is_active = 1;
UPDATE campus_features SET is_active = 1;

-- Mission & Vision Page (already database-driven)
UPDATE mission_vision_hero SET is_active = 1;
UPDATE mission_vision_cards SET is_active = 1;
UPDATE mission_vision_pillars SET is_active = 1;
UPDATE mission_vision_environment SET is_active = 1;
