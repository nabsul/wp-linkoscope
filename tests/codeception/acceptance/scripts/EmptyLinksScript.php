<?php

/**
 * Simple test makes sure main page has no posted links
 */

/* @var $I AcceptanceTester */

$I->wantTo('Check main links page');
$I->amOnPage('link/index');
$I->see('Links Index');
$I->seeInTitle('LinkoScope');
$I->seeLink('LinkoScope');
$I->seeLink('About');
$I->seeLink('Logout');
$I->seeLink('Add New');

$I->wantTo('Check that App is initially empty');
$I->see('No results found');
