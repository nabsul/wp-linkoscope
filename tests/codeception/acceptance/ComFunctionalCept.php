<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->loadSecrets();

$I->amOnPage('/link/index');

include __DIR__ . '/scripts/ComLoginScript.php';

include __DIR__ . '/scripts/EmptyLinksScript.php';

include __DIR__ . '/scripts/AddLinksScript.php';
