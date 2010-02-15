ALTER TABLE  `user`
  ADD UNIQUE (`email`) ;

ALTER TABLE  `user`
  ADD  `first_name` VARCHAR( 128 ) NULL DEFAULT NULL AFTER  `id` ,
  ADD  `last_name` VARCHAR( 128 ) NULL DEFAULT NULL AFTER  `first_name` ;
