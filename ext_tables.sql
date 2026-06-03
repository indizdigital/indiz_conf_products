CREATE TABLE tx_products_domain_model_product (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    subname varchar(255),
    rendertype varchar(255),
    shortdescription varchar(255),
	categories int,
    image int,
    screenshots int,
    description text,
    servicefee_elements varchar(255),
    reference_products varchar(255),
    linked_products varchar(255),
    left_content varchar(255),
    ai_content varchar(255),
    faq varchar(255),
    factsheet int,
    feuser int
);

CREATE TABLE tx_products_domain_model_productelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    price double,
	unit varchar(255),
    min int,
    max int
);

CREATE TABLE tx_products_domain_model_package (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    packagedivs varchar(255)
);

CREATE TABLE tx_products_domain_model_packagediv (
    uid int AUTO_INCREMENT PRIMARY KEY,
    packageelements varchar(255),
    sorting int DEFAULT 0
);

CREATE TABLE tx_products_domain_model_packageelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    amount int,
    package int,
    productelement int,
    sorting int DEFAULT 0
);

CREATE TABLE tx_products_domain_model_category (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    image int,
    description text,
    show_in_menu int
);

CREATE TABLE tx_products_domain_model_oder (
    ordername varchar(255),
    name varchar(255),
    email varchar(255),
    street varchar(255),
    postalcode varchar(255),
    city varchar(255),
    country varchar(255),
    company varchar(255),
    packageUid int,
    productUid int
);

CREATE TABLE tx_products_domain_model_tag (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255)
);


CREATE TABLE tx_products_domain_model_tag (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    description varchar(255)
);
