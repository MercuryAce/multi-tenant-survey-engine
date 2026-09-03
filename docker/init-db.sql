CREATE DATABASE IF NOT EXISTS engagement_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON engagement_test.* TO 'app'@'%';
FLUSH PRIVILEGES;
