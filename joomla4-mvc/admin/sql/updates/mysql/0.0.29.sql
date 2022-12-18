ALTER TABLE `#__helloworld` ADD COLUMN `access` tinyint(4) NOT NULL DEFAULT '0' AFTER `published`;
UPDATE `#__helloworld` SET `access` = 1; 

UPDATE `#__content_types` SET
`field_mappings` = 
'{"common": {
	"core_content_item_id": "id",
	"core_title": "greeting",
	"core_state": "published",
	"core_alias": "alias",
	"core_language":"language", 
	"core_created_time": "created",
	"core_body": "description",
	"core_access": "access",
	"core_catid": "catid"
  }}'
WHERE `type_alias` = 'com_helloworld.helloworld';