-- Database Indexes for Performance Optimization
-- Global Harmony Initiative Website
-- 
-- Run this script to add indexes that will improve query performance
-- Expected improvement: 30-50% faster queries

-- Events table indexes
-- Used for: Filtering by initiative and status, date-based queries
-- Note: IF NOT EXISTS is MySQL 5.7.4+ syntax
CREATE INDEX idx_events_initiative_status 
ON events(initiative_id, status);

CREATE INDEX idx_events_status_date 
ON events(status, event_date);

CREATE INDEX idx_events_date 
ON events(event_date);

-- Initiatives table indexes
-- Used for: Filtering by status, category-based queries
CREATE INDEX idx_initiatives_status 
ON initiatives(status);

CREATE INDEX idx_initiatives_category 
ON initiatives(category);

CREATE INDEX idx_initiatives_status_category 
ON initiatives(status, category);

-- Stories table indexes
-- Used for: Filtering by status, date-based queries, category filtering
CREATE INDEX idx_stories_status_date 
ON stories(status, date);

CREATE INDEX idx_stories_category 
ON stories(category);

CREATE INDEX idx_stories_status_category 
ON stories(status, category);

-- Impact activities table indexes
-- Used for: Filtering by status, event relationships
CREATE INDEX idx_impact_status 
ON impact_activities(status);

CREATE INDEX idx_impact_event_id 
ON impact_activities(event_id);

CREATE INDEX idx_impact_status_event 
ON impact_activities(status, event_id);

-- Causes table indexes (if exists)
-- Used for: Status filtering
CREATE INDEX idx_causes_status 
ON causes(status);

-- Contacts table indexes (if exists)
-- Used for: Status filtering, date queries
-- Note: Uncomment these if contacts table exists
-- CREATE INDEX idx_contacts_status 
-- ON contacts(status);
--
-- CREATE INDEX idx_contacts_date 
-- ON contacts(created_at);

-- Verify indexes were created
-- Run this query to see all indexes:
-- SHOW INDEXES FROM events;
-- SHOW INDEXES FROM initiatives;
-- SHOW INDEXES FROM stories;
-- SHOW INDEXES FROM impact_activities;

