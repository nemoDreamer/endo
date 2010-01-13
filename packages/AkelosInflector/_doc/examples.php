<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title>Akelos PHP Inflector class examples</title>
  <meta http-equiv="Content-Type" content="iso-8859-1" />
 </head>
 <body>
<h1>Akelos PHP Inflector examples</h1>
<?php

require_once 'Inflector.php';

$SingularToPlural = array(
"search"      => "searches",
"switch"      => "switches",
"fix"         => "fixes",
"box"         => "boxes",
"process"     => "processes",
"address"     => "addresses",
"case"        => "cases",
"stack"       => "stacks",
"wish"        => "wishes",
"fish"        => "fish",

"category"    => "categories",
"query"       => "queries",
"ability"     => "abilities",
"agency"      => "agencies",
"movie"       => "movies",

"archive"     => "archives",

"index"       => "indices",

"wife"        => "wives",
"safe"        => "saves",
"half"        => "halves",

"move"        => "moves",

"salesperson" => "salespeople",
"person"      => "people",

"spokesman"   => "spokesmen",
"man"         => "men",
"woman"       => "women",

"basis"       => "bases",
"diagnosis"   => "diagnoses",

"datum"       => "data",
"medium"      => "media",
"analysis"    => "analyses",

"node_child"  => "node_children",
"child"       => "children",

"experience"  => "experiences",
"day"         => "days",

"comment"     => "comments",
"foobar"      => "foobars",
"newsletter"  => "newsletters",

"old_news"    => "old_news",
"news"        => "news",

"series"      => "series",
"species"     => "species",

"quiz"        => "quizzes",

"perspective" => "perspectives",

"ox" => "oxen",
"photo" => "photos",
"buffalo" => "buffaloes",
"tomato" => "tomatoes",
"dwarf" => "dwarves",
"elf" => "elves",
"information" => "information",
"equipment" => "equipment",
"bus" => "buses",
"status" => "statuses",
"mouse" => "mice",

"louse" => "lice",
"house" => "houses",
"octopus" => "octopi",
"virus" => "viri",
"alias" => "aliases",
"portfolio" => "portfolios",

"vertex" => "vertices",
"matrix" => "matrices",

"axis" => "axes",
"testis" => "testes",
"crisis" => "crises",

"rice" => "rice",
"shoe" => "shoes",

"horse" => "horses",
"prize" => "prizes",
"edge" => "edges"
);


echo '<h2>Singular to plural / Plural to singular</h2>';
foreach ($SingularToPlural as $singular=>$plural){
    echo "echo Inflector::pluralize('$singular'); // outputs ".Inflector::pluralize($singular)."<br />";
    echo "echo Inflector::singularize('$plural'); // outputs ".Inflector::singularize($plural)."<br /><br />";
}


$CamelToUnderscore = array(
"Product"               => "product",
"SpecialGuest"          => "special_guest",
"ApplicationController" => "application_controller",
"Area51Controller"      => "area51_controller",
"HTMLTidy"              => "html_tidy",
"HTMLTidyGenerator"     => "html_tidy_generator",
"FreeBSD"               => "free_bsd",
"HTML"                  => "html"
);

echo '<h2>CamelCase to underscore / underscore to CamelCase</h2>';
foreach ($CamelToUnderscore as $camel=>$underscore){
    echo "echo Inflector::underscore('$camel'); // outputs ".Inflector::underscore($camel)."<br />";
    echo "echo Inflector::camelize('$underscore'); // outputs ".Inflector::camelize($underscore)."<br /><br />";
}


$UnderscoreToHuman = array(
"employee_salary" => "Employee salary",
"employee_id"     => "Employee",
"underground"     => "Underground"
);

echo '<h2>Underscore to "human-text" / "Human-text" to Underscore</h2>';
foreach ($UnderscoreToHuman as $underscore=>$human){
    echo "echo Inflector::humanize('$underscore'); // outputs ".Inflector::humanize($underscore)."<br />";
    echo "echo Inflector::underscore('$human'); // outputs ".Inflector::underscore($human)."<br /><br />";
}

$MixtureToTitleCase = array(
'active_record'       => 'Active Record',
'ActiveRecord'        => 'Active Record',
'action web service'  => 'Action Web Service',
'Action Web Service'  => 'Action Web Service',
'Action web service'  => 'Action Web Service',
'actionwebservice'    => 'Actionwebservice',
'Actionwebservice'    => 'Actionwebservice'
);

echo '<h2>Examples of titleize()</h2>';
foreach ($MixtureToTitleCase as $k=>$v){
    echo "echo Inflector::titleize('$k'); // outputs ".Inflector::titleize($k)."<br />";
}

$OrdinalNumbers = array(
"0" => "0th",
"1" => "1st",
"2" => "2nd",
"3" => "3rd",
"4" => "4th",
"5" => "5th",
"6" => "6th",
"7" => "7th",
"8" => "8th",
"9" => "9th",
"10" => "10th",
"11" => "11th",
"12" => "12th",
"13" => "13th",
"14" => "14th",
"20" => "20th",
"21" => "21st",
"22" => "22nd",
"23" => "23rd",
"24" => "24th",
"100" => "100th",
"101" => "101st",
"102" => "102nd",
"103" => "103rd",
"104" => "104th",
"110" => "110th",
"1000" => "1000th",
"1001" => "1001st");

echo '<h2>Examples of ordinalize()</h2>';
foreach ($OrdinalNumbers as $k=>$v){
    echo "echo Inflector::ordinalize($k); // outputs ".Inflector::ordinalize($k)."<br />";
}

?>
 </body>
</html>
