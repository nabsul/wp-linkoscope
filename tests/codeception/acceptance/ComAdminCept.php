<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario, 'com');
$adminParams = ['clientId', 'clientSecret', 'redirectUrl', 'blogId', 'blogUrl', 'adminToken'];

include __DIR__ . '/_adminScript.php';
