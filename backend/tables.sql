DROP TABLE IF EXISTS User;
CREATE TABLE User(idUser INTEGER AUTO_INCREMENT,
                  username VARCHAR(32) NOT NULL,
                  passwd VARCHAR(64) NOT NULL,
                  CONSTRAINT pkUser PRIMARY KEY (idUser),
                  CONSTRAINT ckPasswdLength CHECK (LENGTH(passwd) = 64));
