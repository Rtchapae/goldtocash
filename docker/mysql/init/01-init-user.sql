-- Create database and user for Gold to Cash application
CREATE DATABASE IF NOT EXISTS goldtocash CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'goldtocash_user'@'%' IDENTIFIED BY 'goldtocash_password';
GRANT ALL PRIVILEGES ON goldtocash.* TO 'goldtocash_user'@'%';
FLUSH PRIVILEGES;