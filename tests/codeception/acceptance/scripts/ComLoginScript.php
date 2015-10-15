<?php

/**
 * This is the script needed to log into
 * a site configured on a wp.com site.
 */

/* @var $I AcceptanceTester */

$I->click('Login');
$I->seeInCurrentUrl('oauth2/authorize');
$I->see('Howdy!');
$I->see('Email or UserName');
$I->see('Password');

$I->fillField('log', $I->secrets->comApi->admin->username);
$I->fillField('pwd', $I->secrets->comApi->admin->password);
$I->click('button[type=submit]');

$I->seeInCurrentUrl('oauth2/authorize');
$I->see('Howdy!');
$I->see('Deny');
$I->see('Approve');
$I->click('Approve');

$I->seeInCurrentUrl('web/link');

