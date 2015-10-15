<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('LinkoScope');
$I->seeInTitle('LinkoScope');
$I->seeLink('Login');
$I->see('Links Index');
