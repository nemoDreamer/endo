<?php

test('Story', 'story', AppInflector::classify('story', 'model'));
test('Story', 'stories', AppInflector::classify('stories', 'model'));

test('ManualChaptersController', 'manual_chapter', AppInflector::classify('manual_chapter', 'controller'));
test('ManualChaptersController', 'Manual_Chapters', AppInflector::classify('Manual_Chapters', 'controller'));
test('ManualChaptersController', 'ManualChapter', AppInflector::classify('ManualChapter', 'controller'));

test('SalesPeopleController', 'sales_person', AppInflector::classify('sales_person', 'controller'));
test('SalesPeopleController', 'sales_people', AppInflector::classify('sales_people', 'controller'));

test('SalesPerson', 'sales_person', AppInflector::classify('sales_person', 'model'));
test('SalesPerson', 'SalesPeople', AppInflector::classify('SalesPeople', 'model'));

test('SalesPerson', 'SalesPeople', Inflector::classify('SalesPeople'));
test('SalesPerson', 'Sales People', Inflector::classify('Sales People'));

test('Sales People', 'Sales Person', Inflector::pluralize('Sales Person'));

test('SalesPerson', 'Sales People', Inflector::classify('Sales People'));



function test($intended, $input, $result)
{
  echo "<pre class=\"test ".($result==$intended?'success':'failure')."\">$input -> $result ($intended)</pre>";
}

?>
<style type="text/css" media="screen">
  pre.test {
    margin: 10px 0;
    padding: 10px;
  }
  pre.success {
    background-color: #080;
    color: #8f8;
  }
  pre.failure {
    background-color: #800;
    color: #f88;
  }
</style>