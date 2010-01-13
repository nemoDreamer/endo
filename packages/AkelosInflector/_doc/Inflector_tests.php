<?php

error_reporting(E_ALL);

require_once '../Inflector.php';

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');


class Test_of_Inflector extends  UnitTestCase
{
    var $SingularToPlural = array(
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

    var $CamelToUnderscore = array(
    "Product"               => "product",
    "SpecialGuest"          => "special_guest",
    "ApplicationController" => "application_controller",
    "Area51Controller"      => "area51_controller",
    );

    var $CamelToUnderscoreWithoutReverse = array(
    "HTMLTidy"              => "html_tidy",
    "HTMLTidyGenerator"     => "html_tidy_generator",
    "FreeBSD"               => "free_bsd",
    "HTML"                  => "html",
    );

    var $ClassNameToTableName = array(
    "PrimarySpokesman" => "primary_spokesmen",
    "NodeChild"        => "node_children"
    );

    var $UnderscoreToHuman = array(
    "employee_salary" => "Employee salary",
    "employee_id"     => "Employee",
    "underground"     => "Underground"
    );

    var $MixtureToTitleCase = array(
    'active_record'       => 'Active Record',
    'ActiveRecord'        => 'Active Record',
    'action web service'  => 'Action Web Service',
    'Action Web Service'  => 'Action Web Service',
    'Action web service'  => 'Action Web Service',
    'actionwebservice'    => 'Actionwebservice',
    'Actionwebservice'    => 'Actionwebservice'
    );

    var $OrdinalNumbers = array(
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
    "1001" => "1001st"
    );

    function Test_of_pluralize_plurals()
    {
        $this->assertEqual('plurals', Inflector::pluralize("plurals"));
        $this->assertEqual('Plurals', Inflector::pluralize("Plurals"));
    }

    function Test_of_pluralize_singular()
    {
        foreach ($this->SingularToPlural as $singular=>$plural){
            $this->assertEqual($plural, Inflector::pluralize($singular));
            $this->assertEqual(ucfirst($plural), Inflector::pluralize(ucfirst($singular)));
        }
    }

    function Test_of_singularize_plural()
    {
        foreach ($this->SingularToPlural as $singular=>$plural){
            $this->assertEqual($singular, Inflector::singularize($plural));
            $this->assertEqual(ucfirst($singular), Inflector::singularize(ucfirst($plural)));
        }
    }

    function Test_of_titleize()
    {
        foreach ($this->MixtureToTitleCase as $source=>$expected){
            $this->assertEqual($expected, Inflector::titleize($source));
        }
    }

    function Test_of_camelize()
    {
        foreach ($this->CamelToUnderscore as $camel=>$underscore){
            $this->assertEqual($camel, Inflector::camelize($underscore));
        }
    }

    function Test_of_underscore()
    {
        foreach ($this->CamelToUnderscore as $camel=>$underscore){
            $this->assertEqual($underscore, Inflector::underscore($camel));
        }

        foreach ($this->CamelToUnderscoreWithoutReverse as $camel=>$underscore){
            $this->assertEqual($underscore, Inflector::underscore($camel));
        }
    }

    function Test_of_tableize()
    {
        foreach ($this->ClassNameToTableName as $class_name=>$table_name){
            $this->assertEqual($table_name, Inflector::tableize($class_name));
        }
    }

    function Test_of_classify()
    {
        foreach ($this->ClassNameToTableName as $class_name=>$table_name){
            $this->assertEqual($class_name, Inflector::classify($table_name));
        }
    }

    function Test_of_humanize()
    {
        foreach ($this->UnderscoreToHuman as $underscore=>$human){
            $this->assertEqual($human, Inflector::humanize($underscore));
        }
    }

    function Test_of_ordinalize()
    {
        foreach ($this->OrdinalNumbers as $number=>$ordinalized){
            $this->assertEqual($ordinalized, Inflector::ordinalize($number));
        }
    }

}


$test = new Test_of_Inflector();
$test->run(new HtmlReporter());


?>
