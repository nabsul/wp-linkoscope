<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->setSiteData(17);

$I->amOnPage('/');
$I->login();

$I->amOnPage('/link/index?pageSize=5');
$I->see('17 items');

$I->seeLink('1', "ul.pagination");
$I->seeLink('2', "ul.pagination");
$I->seeLink('3', "ul.pagination");
$I->seeLink('4', "ul.pagination");
$I->dontSeeLink('5', "ul.pagination");
$I->dontSeeLink('6', "ul.pagination");

$I->seeInRow(0, 'Test Title #17');
$I->seeInRow(1, 'Test Title #16');
$I->seeInRow(2, 'Test Title #15');
$I->seeInRow(3, 'Test Title #14');
$I->seeInRow(4, 'Test Title #13');
$I->dontSeeRow(5);

$I->click("a[data-page='2']");

$I->seeInRow(0, 'Test Title #7');
$I->seeInRow(1, 'Test Title #6');
$I->seeInRow(2, 'Test Title #5');
$I->seeInRow(3, 'Test Title #4');
$I->seeInRow(4, 'Test Title #3');
$I->dontSeeRow(5);

$I->click("a[data-page='3']");

$I->seeInRow(0, 'Test Title #2');
$I->seeInRow(1, 'Test Title #1');
$I->dontSeeRow(2);
$I->dontSeeRow(3);
$I->dontSeeRow(4);
