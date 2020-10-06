-- PLEASE NOTE THAT THIS SQL IS NOT TESTED AND IS BETTER USED AS A REFERENCE

-- Create a temp table that duplicates the user_items table
CREATE TABLE `db_name`.`temp` SELECT `*` FROM `db_name`.`user_items`;

-- Update the count column of all user_items rows
-- This does not remove duplicates, but each duplicate should now reflect the total count before this statement was executed
UPDATE `db_name`.`user_items` AS t
SET t.`count` = (SELECT SUM(t1.`count`) FROM `db_name`.`temp` t1 WHERE t1.`item_id` = t.`item_id` AND t1.`user_id` = t.`user_id` AND t1.`data` = t.`data`);

-- Drop and re-add the foreign key temporarily, implementing ON DELETE CASCADE to remove item logs related to the removed rows
ALTER TABLE `db_name`.`user_items_log`
DROP FOREIGN KEY `user_items_log_stack_id_foreign`;

ALTER TABLE `db_name`.`user_items_log`
ADD CONSTRAINT `user_items_log_stack_id_foreign`
FOREIGN KEY (`stack_id`) REFERENCES `db_name`.`user_items` (`id`) ON DELETE CASCADE;

-- Delete all duplicate rows
DELETE FROM `db_name`.`user_items` WHERE `db_name`.`user_items`.`id` not in
(SELECT * FROM
	(SELECT min(`db_name`.`user_items`.`id`) FROM `db_name`.`user_items` GROUP BY `db_name`.`user_items`.`item_id`, `db_name`.`user_items`.`user_id`, `db_name`.`user_items`.`data`) as temp_tab
);

-- Change the foreign key back
ALTER TABLE `db_name`.`user_items_log`
DROP FOREIGN KEY `user_items_log_stack_id_foreign`;

ALTER TABLE `db_name`.`user_items_log`
ADD CONSTRAINT `user_items_log_stack_id_foreign`
FOREIGN KEY (`stack_id`) REFERENCES `db_name`.`user_items` (`id`);

-- Drop the temp table
DROP TABLE `db_name`.`temp`;