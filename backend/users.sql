DROP USER IF EXISTS 'panda_store'@'localhost';
CREATE USER 'panda_store'@'localhost' IDENTIFIED WITH mysql_native_password BY 'panda_store';
GRANT ALL ON *.* TO 'panda_store'@'localhost';