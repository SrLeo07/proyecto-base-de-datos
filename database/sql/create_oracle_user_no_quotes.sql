--- Drop any existing user first (optional, uncomment if needed)
--- DROP USER LEO CASCADE;

-- Create user without quotes, uppercase by Oracle convention
CREATE USER LEO IDENTIFIED BY 0770;
GRANT CREATE SESSION TO LEO;
-- Grant basic object privileges used by Laravel migrations
GRANT CREATE TABLE TO LEO;
GRANT CREATE SEQUENCE TO LEO;
GRANT CREATE VIEW TO LEO;
GRANT CREATE TRIGGER TO LEO;
-- Optional: give more privileges if required
GRANT UNLIMITED TABLESPACE TO LEO;
GRANT RESOURCE TO LEO;

-- After running the above, use DB_USERNAME=LEO in .env (uppercase)