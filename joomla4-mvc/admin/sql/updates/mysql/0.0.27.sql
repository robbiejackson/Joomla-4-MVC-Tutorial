ALTER TABLE `#__helloworld` ADD COLUMN `description` VARCHAR(4000) NOT NULL DEFAULT '' AFTER `greeting`;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `rules`, `field_mappings`, `content_history_options`) 
VALUES
('Helloworld', 'com_helloworld.helloworld', '', '',
'{"formFile":"administrator\\/components\\/com_helloworld\\/models\\/forms\\/helloworld.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path"], 
"ignoreChanges":["checked_out", "checked_out_time", "path"],
"convertToInt":[], 
"displayLookup":[
{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__helloworld","targetColumn":"id","displayColumn":"greeting"},
{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}'),
('Helloworld Category', 'com_helloworld.category', '', '',
'{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');