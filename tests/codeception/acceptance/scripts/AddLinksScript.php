<?php

/**
 * This script adds three links to an empty WebApp.
 * It also tests how the order should change when votes are made.
 */

/* @var $I AcceptanceTester */

$title1 = 'Link Title 1';
$url1 = 'http://url1.com/1/1';
$title2 = 'Link Title 2';
$url2 = 'http://url2.com/2/2';

include __DIR__ . '/EmptyLinksScript.php';

$I->addNewLink($title1, $url1);

$I->seeInCurrentUrl('link/index');
$I->seeInRow(0, $title1);
$I->seeLink($title1, $url1);
$I->dontSeeRow(1);

$I->addNewLink($title2, $url2);

$I->seeLink($title1, $url1);
$I->seeLink($title2, $url2);

$I->seeInRow(0, $title2);
$I->seeInRow(1, $title1);
$I->dontSeeRow(2);

$I->clickRowAction(0, 'View');
$I->see($title2);
$I->dontSee($title1);
$I->moveBack();

$I->clickRowAction(1, 'View');
$I->see($title1);
$I->dontSee($title2);
$I->moveBack();

$I->seeInRow(0, '0 votes');
$I->seeInRow(1, '0 votes');

$I->clickRowAction(1, 'Up');
$I->seeInRow(0, $title1);
$I->seeInRow(0, '1 votes');
$I->seeInRow(1, '0 votes');

$I->clickRowAction(0, 'Down');
$I->seeInRow(0, $title2);
$I->seeInRow(1, $title1);
$I->seeInRow(0, '0 votes');
$I->seeInRow(1, '0 votes');
