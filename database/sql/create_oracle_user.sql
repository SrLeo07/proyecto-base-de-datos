-- Create an application user for the Laravel app
-- Run this as a DBA (e.g. SYSTEM) in SQL*Plus / SQL Developer
-- Replace PASSWORD_HERE with a strong password (example uses 0770 per your note)

CREATE USER leo IDENTIFIED BY 0770;
GRANT CREATE SESSION TO leo;
-- Grant basic object privileges used by Laravel migrations
GRANT CREATE TABLE TO leo;
GRANT CREATE SEQUENCE TO leo;
GRANT CREATE VIEW TO leo;
GRANT CREATE TRIGGER TO leo;
-- Optional: give more privileges if required
GRANT UNLIMITED TABLESPACE TO leo;

-- After running the above, switch to the new user and run migrations from Laravel
-- or run migrations as SYSTEM specifying the 'oracle' connection with DB_USERNAME=leo/DB_PASSWORD=0770 in .env
