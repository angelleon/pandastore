DROP TABLE IF EXISTS User;
CREATE TABLE User(idUser INTEGER AUTO_INCREMENT,
                  email VARCHAR(32) NOT NULL,
                  passwd VARCHAR(64) NOT NULL,
                  givenName VARCHAR(100) NOT NULL,
                  surname VARCHAR(100) NOT NULL,
                  active BOOLEAN NOT NULL DEFAULT TRUE,
                  createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  updatedAt TIMESTAMP DEFAULT NULL,
                  CONSTRAINT pkUser PRIMARY KEY (idUser),
                  CONSTRAINT ckPasswdLength CHECK (LENGTH(passwd) = 64));

DROP TABLE IF EXISTS Product;
CREATE TABLE Product(idProduct INTEGER AUTO_INCREMENT,
                     name VARCHAR(150),
                     price DECIMAL(5,2),
                     photoHash VARCHAR(64) NOT NULL,
                     description VARCHAR(500),
                     tags VARCHAR(500),
                     active BOOLEAN NOT NULL DEFAULT TRUE,
                     createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     updatedAt TIMESTAMP DEFAULT NULL,
                     CONSTRAINT pkProduct PRIMARY KEY(idProduct));
