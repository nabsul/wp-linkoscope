<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario, 'org');
$adminParams = ['consumerKey', 'consumerSecret', 'blogUrl'];

include __DIR__ . '/_adminScript.php';
