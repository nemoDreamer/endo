CREATE TABLE  `event` (
  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `user_id` INT( 11 ) NULL DEFAULT NULL ,
  `model_class` VARCHAR( 128 ) NULL DEFAULT NULL ,
  `model_id` INT( 11 ) NULL DEFAULT NULL ,
  `action_id` INT( 11 ) NULL DEFAULT NULL ,
  `timestamp` TIMESTAMP NULL DEFAULT NULL ,
  INDEX (  `user_id` )
) ENGINE = MYISAM ;

-- CREATE TABLE  `eventaction` (
--   `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
--   `slug` VARCHAR( 32 ) NULL DEFAULT NULL ,
--   `full` VARCHAR( 256 ) NULL DEFAULT NULL
-- ) ENGINE = MYISAM ;
-- 
-- INSERT INTO `eventaction` VALUES(NULL, 'add', 'added');
-- INSERT INTO `eventaction` VALUES(NULL, 'edit', 'edited');
-- INSERT INTO `eventaction` VALUES(NULL, 'remove', 'removed');
-- INSERT INTO `eventaction` VALUES(NULL, 'show', 'viewed');
-- INSERT INTO `eventaction` VALUES(NULL, 'login', 'logged in');
-- INSERT INTO `eventaction` VALUES(NULL, 'logout', 'logged out');
-- INSERT INTO `eventaction` VALUES(NULL, 'signup', 'signed up');
-- INSERT INTO `eventaction` VALUES(NULL, 'forgot_password', 'forgot their password');
-- INSERT INTO `eventaction` VALUES(NULL, 'reset_password', 'reset their password');

ALTER TABLE  `event` ADD  `subject_class` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `id` ;

ALTER TABLE  `event`
  CHANGE  `user_id`  `subject_id` INT( 11 ) ,
  CHANGE  `model_class`  `object_class` VARCHAR( 32 ) ,
  CHANGE  `model_id`  `object_id` INT( 11 ) ;

ALTER TABLE  `event` CHANGE  `action_id`  `action` VARCHAR( 32 ) NULL DEFAULT NULL ;

ALTER TABLE  `event` CHANGE  `timestamp`  `timestamp` DATETIME NULL DEFAULT NULL ;