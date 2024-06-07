CREATE TABLE db_tokens (
   org_key VARCHAR(128),
   api_uri VARCHAR(256),
   token LONGTEXT,
   refresh LONGTEXT,
   expires VARCHAR(16),
   last_updated VARCHAR(32)
);