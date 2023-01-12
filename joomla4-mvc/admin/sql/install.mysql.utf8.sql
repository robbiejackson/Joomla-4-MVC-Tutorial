DROP TABLE IF EXISTS `#__helloworld`;

CREATE TABLE `#__helloworld` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10)     NOT NULL DEFAULT '0',
	`created`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out` INT(10) NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`greeting` VARCHAR(25) NOT NULL,
	`description` VARCHAR(4000) NOT NULL DEFAULT '',
	`alias`  VARCHAR(40)  NOT NULL DEFAULT '',
    `language`  CHAR(7)  NOT NULL DEFAULT '*',
	`parent_id`	int(10)    NOT NULL DEFAULT '1',
	`level`	int(10)    NOT NULL DEFAULT '0',
	`path`	VARCHAR(400)    NOT NULL DEFAULT '',
	`lft`	int(11)    NOT NULL DEFAULT '0',
	`rgt`	int(11)    NOT NULL DEFAULT '0',
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`access` tinyint(4) NOT NULL DEFAULT '0',
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
	`image`   VARCHAR(1024) NOT NULL DEFAULT '',
	`latitude` DECIMAL(9,7) NOT NULL DEFAULT 0.0,
	`longitude` DECIMAL(10,7) NOT NULL DEFAULT 0.0,
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

CREATE UNIQUE INDEX `aliasindex` ON `#__helloworld` (`alias`, `catid`);

INSERT INTO `#__helloworld` (`greeting`,`alias`,`language`, `parent_id`, `level`, `path`, `lft`, `rgt`, `published`) VALUES
('helloworld root','helloworld-root-alias','*', 0, 0, '', 0, 5, 1),
('Hello World!','hello-world','en-GB', 1, 1, 'hello-world', 1, 2, 0),
('Goodbye World!','goodbye-world','en-GB', 1, 1, 'goodbye-world', 3, 4, 0);

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `rules`, `content_history_options`, `table`, `field_mappings`, `router`) 
VALUES
('Helloworld', 'com_helloworld.helloworld', '',
'{"formFile":"administrator\\/components\\/com_helloworld\\/forms\\/helloworld.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path"], 
"ignoreChanges":["checked_out", "checked_out_time", "path"],
"convertToInt":[], 
"displayLookup":[
{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__helloworld","targetColumn":"id","displayColumn":"greeting"},
{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
'{"special":{"dbtable":"#__helloworld","key":"id","type":"HelloworldTable","prefix":"Robbie\\\\Component\\\\Helloworld\\\\Administrator\\\\Table\\\\","config":"array()"},
"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"Joomla\\\\CMS\\\\Table\\\\","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "greeting",
    "core_state": "published",
    "core_alias": "alias",
    "core_language":"language", 
    "core_created_time": "created",
    "core_body": "description",
    "core_access":"access", 
    "core_catid": "catid"
  }}',
'HelloworldHelperRoute::getHelloworldRoute'),
('Helloworld Category', 'com_helloworld.category', '',
'{"formFile":"administrator\\/components\\/com_categories\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
'{"special":{"dbtable":"#__categories","key":"id","type":"CategoryTable","prefix":"Joomla\\\\Component\\\\Categories\\\\Administrator\\\\Table\\\\","config":"array()"},
"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"Joomla\\\\CMS\\\\Table\\\\","config":"array()"}}',
'{"common": {
	"core_content_item_id":"id",
	"core_title":"title",
	"core_state":"published",
	"core_alias":"alias",
	"core_created_time":"created_time",
	"core_modified_time":"modified_time",
	"core_body":"description", 
	"core_hits":"hits",
	"core_publish_up":"null",
	"core_publish_down":"null",
	"core_access":"access", 
	"core_params":"params", 
	"core_featured":"null", 
	"core_metadata":"metadata", 
	"core_language":"language", 
	"core_images":"null", 
	"core_urls":"null", 
	"core_version":"version",
	"core_ordering":"null", 
	"core_metakey":"metakey", 
	"core_metadesc":"metadesc", 
	"core_catid":"parent_id", 
	"core_xreference":"null", 
	"asset_id":"asset_id"}, 
  "special":{
    "parent_id":"parent_id",
	"lft":"lft",
	"rgt":"rgt",
	"level":"level",
	"path":"path",
	"extension":"extension",
	"note":"note"}}',
'HelloworldHelperRoute::getCategoryRoute');