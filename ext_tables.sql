CREATE TABLE tx_products_domain_model_product (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    subname varchar(255),
    rendertype varchar(255),
    shortdescription text,
	categories int,
    image int,
    subimage int,
    screenshots varchar(255),
    description text,
    reference_products varchar(255),
    altcontent varchar(255),
    accordeon varchar(255),
    ai_content varchar(255),
    faq varchar(255),
    feuser int
);

CREATE TABLE tx_products_domain_model_productelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    elementtype int,
    price double,
	unit varchar(255),
    min int,
    max int
);

CREATE TABLE tx_products_domain_model_package (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    configurable int,
    packageelements varchar(255)
);

CREATE TABLE tx_products_domain_model_packageelement (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    formula varchar(255),
    desc text,
    amount int,
    min int,
    max int,
    package int,
    sorting int DEFAULT 0
);

CREATE TABLE tx_products_domain_model_category (
    uid int AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    image int,
    description text,
    show_in_menu int
);

CREATE TABLE tx_products_domain_model_order (
    ordername varchar(255),
    firstname varchar(255),
    name varchar(255),
    gender varchar(255),
    email varchar(255),
    addressline varchar(255),
    addressline2 varchar(255),
    postalcode varchar(255),
    phone varchar(255),
    city varchar(255),
    country varchar(255),
    company varchar(255),
    ordertype int,
    total double,
    package_uid int,
    product_uid int,
    agb int DEFAULT 0,
    newsletter int DEFAULT 0,
    data text
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
