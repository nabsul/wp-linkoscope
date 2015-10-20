<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage('/');
$I->see('LinkoScope');
$I->seeInTitle('LinkoScope');
$I->seeLink('Login');
$I->see('Shared Links:');
