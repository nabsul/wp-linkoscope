<?php

/**
 * Code needed to sign into a self-hosted WP blog
 */

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->wantTo('check that main page is working');
$I->amOnPage('/');
$I->see('LinkoScope');

$I->click('Login');
$I->wait(3);

$I->seeInCurrentUrl('/wp-login.php');
$I->see('Lost your password');
$I->see('Back to');
$I->see('UserName');
$I->see('Password');

$I->fillField('log', 'nabeel');
$I->fillField('pwd', 'nabeel');
$I->click('#wp-submit');
$I->wait(3);

$I->seeInCurrentUrl('/wp-login.php');
$I->see('Howdy');
$I->see('would like to connect to');
$I->see('Authorize');
$I->see('Cancel');
$I->see('Switch user');
$I->click('Authorize');
$I->wait(3);

$I->seeInCurrentUrl('link/index');
$I->see('Links Index');
$I->see('Add New');

