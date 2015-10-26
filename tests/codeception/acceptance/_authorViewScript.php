<?php

use ShortCirquit\LinkoScopeApi\iLinkoScope;
use ShortCirquit\LinkoScopeApi\Models\Link;

/* @var $I AcceptanceTester */
/* @var $api iLinkoScope */

$I->deleteAllLinks();
$api = Yii::$app->linko->getConsoleApi();

$users = $api->getAccounts();
$user1 = $users[0];
$user2 = $users[1];

$link = new Link();
$link->title = "{$user1->name}'s, post title 1";
$link->url = 'http://user1.com/url';
$link->authorId = $user1->id;
$api->addLink($link);

$I->wait(2);

$link = new Link();
$link->title = "{$user2->name}'s, post title 2";
$link->url = 'http://user2.com/url';
$link->authorId = $user2->id;
$api->addLink($link);

$I->amOnPage('/');
$I->login();
$I->see('1-2 of 2 items');
$I->seeInRow(0, $user2->name);
$I->seeInRow(0, 'post title 2');
$I->seeInRow(1, $user1->name);
$I->seeInRow(1, 'post title 1');

$I->clickInRow(0, $user2->name);
$I->see('post title 2');
$I->dontSee('post title 1');
$I->moveBack();

$I->clickInRow(1, $user1->name);
$I->see('post title 1');
$I->dontSee('post title 2');
$I->moveBack();
