<?php

/** @var AcceptanceTester $I */

$I->amGoingTo('admin login page');
$I->amOnPage('/admin');
$I->canSee('Admin Login');
$I->canSee('Username');
$I->canSee('Password');

$I->amGoingTo('enter admin credentials');
$I->fillField('LoginForm[username]', 'admin');
$I->fillField('LoginForm[password]', $I->getAdminPass());
$I->click('button[type=submit]');
$I->wait(3);

$I->wantTo('see API configuration parameters');
$I->see('Admin');
$I->see('Current config');

foreach ($adminParams as $k){
    $I->see("[$k]");
}

$I->see('WP Com Setup');
$I->see('WP Org Setup');

$I->click('Logout');
$I->wait(3);

$I->see('Shared Links:');
