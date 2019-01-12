ALTER TABLE `mis_travel_detail` CHANGE `adult_pay` `adult_pay` INT(11) NULL, CHANGE `child_pay` `child_pay` INT(11) NULL, CHANGE `student_pay` `student_pay` INT(11) NULL, CHANGE `adult_price` `adult_price` DECIMAL(10,2)NULL, CHANGE `child_price` `child_price` DECIMAL(10,2) NULL, CHANGE `student_price` `student_price` DECIMAL(10,2) NULL, CHANGE `adult_except` `adult_except` INT(11) NULL, CHANGE `child_except` `child_except` INT(11) NULL,CHANGE `student_except` `student_except` INT(11) NULL;

ALTER TABLE `mis_travel_detail`  ADD `except_amount` INT NULL  AFTER`student_except`,  ADD `except_prices` DECIMAL(16,2) NULL  AFTER`except_amount`,  ADD `student_amount` INT NULL  AFTER `except_prices`,  ADD`student_prices` DECIMAL(16,2) NULL  AFTER `student_amount`,  ADD `adult_amount`INT NULL  AFTER `student_prices`,  ADD `adult_prices` DECIMAL(16,2) NULL  AFTER`adult_amount`,  ADD `total_amount` INT NULL  AFTER `adult_prices`,  ADD`total_prices` DECIMAL(16,2) NULL  AFTER `total_amount`,  ADD  INDEX(`except_amount`),  ADD  INDEX  (`except_prices`),  ADD  INDEX  (`student_amount`),  ADDINDEX  (`student_prices`),  ADD  INDEX  (`adult_amount`),  ADD  INDEX  (`adult_prices`),ADD  INDEX  (`total_prices`),  ADD  INDEX  (`total_amount`);

ALTER TABLE `mis_travel_detail` CHANGE `travel_type_id` `travel_type_id` INT(11) NULL;

INSERT INTO `mis_menu` (`menu_name_th`, `menu_name_en`, `parent_menu`, `menu_type`, `actives`, `menu_url`, `menu_order`, `menu_logo`) VALUES
('ข้อมูลสหกรณ์', 'ข้อมูลสหกรณ์', 24, 'PAGE', 'Y', 'cooperative', 13, NULL);