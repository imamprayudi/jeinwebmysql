USE ediweb;

ALTER TABLE `ediweb`.`log1` 
ADD COLUMN `ipserver` VARCHAR(50) NULL DEFAULT NULL AFTER `waktu`,
ADD COLUMN `ipclient` VARCHAR(20) NULL DEFAULT NULL AFTER `ipserver`;

CREATE TABLE mailpo_confirmation (
	id int NOT NULL AUTO_INCREMENT,
	transmission_no varchar(20) NULL,
	transmission_date datetime NULL,
	supplier_code varchar(10) NULL,
	po_number varchar(15) NULL,
	status varchar(50) NULL,
	read_at datetime NULL,
	supplier_confirmed_status varchar(50) NULL,
	supplier_confirmed_reason varchar(255) NULL,
	supplier_confirmed_by varchar(50) NULL,
	supplier_confirmed_at datetime NULL,
	purch_confirmed_status varchar(50) NULL,
	purch_confirmed_reason varchar(255) NULL,
	purch_confirmed_by varchar(50) NULL,
	purch_confirmed_at datetime NULL,
	mc_confirmed_status varchar(50) NULL,
	mc_confirmed_reason varchar(255) NULL,
	mc_confirmed_by varchar(50) NULL,
	mc_confirmed_at datetime NULL,
	user_secure int NULL,
    PRIMARY KEY (id)
);

CREATE TABLE mailpoc_confirmation(
	id int NOT NULL AUTO_INCREMENT,
	transmission_no varchar(20) NULL,
	transmission_date datetime NULL,
	supplier_code varchar(10) NULL,
	po_number varchar(15) NULL,
	status varchar(50) NULL,
	read_at datetime NULL,
	supplier_confirmed_status varchar(50) NULL,
	supplier_confirmed_reason varchar(255) NULL,
	supplier_confirmed_by varchar(50) NULL,
	supplier_confirmed_at datetime NULL,
	purch_confirmed_status varchar(50) NULL,
	purch_confirmed_reason varchar(255) NULL,
	purch_confirmed_by varchar(50) NULL,
	purch_confirmed_at datetime NULL,
	mc_confirmed_status varchar(50) NULL,
	mc_confirmed_reason varchar(255) NULL,
	mc_confirmed_by varchar(50) NULL,
	mc_confirmed_at datetime NULL,
	user_secure int NULL,
    PRIMARY KEY (id)
 );






