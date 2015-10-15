<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->amGoingTo('admin login page');
$I->amOnPage('/admin');
$I->canSee('Admin Login');
$I->canSee('Username');
$I->canSee('Password');

$I->amGoingTo('enter admin credentials');
$I->fillField('LoginForm[username]', 'admin');
$I->fillField('LoginForm[password]', 'ChangeMePlease');
$I->click('button[type=submit]');
$I->wait(3);

$I->wantTo('see API configuration parameters');
$I->see('Admin');
$I->see('Current config');

$cfg = json_decode(file_get_contents(Yii::$app->runtimePath . '/api.cfg'), true);
unset($cfg['type']);
foreach ($cfg as $k => $v){
    $I->see("[$k] => $v");
}

$I->see('WP Com Setup');
$I->see('WP Org Setup');

$I->click('Logout');
$I->wait(3);

$I->see('Links Index');
