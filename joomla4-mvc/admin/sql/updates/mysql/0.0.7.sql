DROP TABLE IF EXISTS `#__helloworld`;

CREATE TABLE `#__helloworld` ( 
    `id` SERIAL NOT NULL, 
    `greeting` VARCHAR(200) NOT NULL, 
    `published` BOOLEAN NOT NULL DEFAULT FALSE, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB; 

INSERT INTO `#__helloworld` (`greeting`) VALUES
    ('Hello World!'),
    ('Good bye World!');