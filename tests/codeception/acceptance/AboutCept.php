<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->amOnPage('/');
$I->wantTo('ensure that about works');
$I->click('About');
$I->see('LinkoScope', 'h1');
$I->see('WebApp');
$I->see('WordPress.com API', 'a');
$I->see('plugin API', 'a');
$I->see('Hacker News', 'a');
$I->see('scoring');
