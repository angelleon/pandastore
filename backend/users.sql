DROP USER IF EXISTS 'panda_store'@'%';
CREATE USER 'panda_store'@'%' IDENTIFIED WITH mysql_native_password USING PASSWORD('panda_store');
GRANT ALL ON *.* TO 'panda_store'@'%';