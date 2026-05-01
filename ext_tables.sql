CREATE TABLE tx_ext_domain_model_product (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    description text
);

CREATE TABLE tx_ext_domain_model_productelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    price double,
    min int,
    max int
);

CREATE TABLE tx_ext_domain_model_package (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    product int
);

CREATE TABLE tx_ext_domain_model_packageelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    amount int,
    is_base tinyint(1),
    package int,
    productelement int
);