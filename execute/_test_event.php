<?php

$Doctor = AppModel::FindFirst('Doctor', false);
$ManualPage = AppModel::FindFirst('ManualPage', false);
$ManualChapter = AppModel::FindFirst('ManualChapter', false);

Event::Set($Doctor, 'read', $ManualPage);
Event::Set($Doctor, 'complete', $ManualChapter);

d_arr(AppModel::FindAll('Event'));

?>