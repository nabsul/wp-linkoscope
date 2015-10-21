<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);

$I->amOnPage('/link/index');

if ($I->getConfig()->type == 'com'){
    $I->click('Login');
    $I->seeInCurrentUrl('oauth2/authorize');
    $I->see('Howdy!');
    $I->see('Email or UserName');
    $I->see('Password');

    $I->fillField('log', $I->getConfig()->username);
    $I->fillField('pwd', $I->getConfig()->password);
    $I->click('button[type=submit]');

    $I->seeInCurrentUrl('oauth2/authorize');
    $I->see('Howdy!');
    $I->see('Deny');
    $I->see('Approve');
    $I->click('Approve');

    $I->seeInCurrentUrl('web/link');
}else{
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

    $I->fillField('log', $I->getConfig()->username);
    $I->fillField('pwd', $I->getConfig()->password);
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
    $I->see('Shared Links:');
    $I->see('Add New');
}

$I->wantTo('Check main links page');
$I->amOnPage('link/index');
$I->see('Shared Links:');
$I->seeInTitle('LinkoScope');
$I->seeLink('LinkoScope');
$I->seeLink('About');
$I->seeLink('Logout');
$I->see('Add New');

$I->wantTo('Check that App is initially empty');
$I->see('No results found');

$links = [
    ['title' => 'Link Title 1', 'url' => 'http://url1.com/1/1'],
    ['title' => 'Link Title 2', 'url' => 'http://url2.com/2/2'],
];

$I->wantTo('add a link');
$I->addNewLink($links[0]['title'], $links[0]['url']);

$I->seeInCurrentUrl('link/index');
$I->seeInRow(0, $links[0]['title']);
$I->seeLink($links[0]['title'], $links[0]['url']);
$I->dontSeeRow(1);

$I->wantTo('add a second link');
$I->addNewLink($links[1]['title'], $links[1]['url']);

$I->seeLink($links[0]['title'], $links[0]['url']);
$I->seeLink($links[1]['title'], $links[1]['url']);

$I->wantTo('check the order of the links');

$I->seeInRow(0, $links[1]['title']);
$I->seeInRow(1, $links[0]['title']);
$I->dontSeeRow(2);

$I->wantTo("view second link's details");
$I->seeInRow(0, 'discuss');
$I->clickInRow(0, 'discuss');
$I->see($links[1]['title']);
$I->dontSee($links[0]['title']);
$I->moveBack();

$I->wantTo("view first link's details");
$I->seeInRow(1, 'discuss');
$I->clickInRow(1, 'discuss');
$I->see($links[0]['title']);
$I->dontSee($links[1]['title']);
$I->moveBack();

$I->wantTo('try voting');
$I->seeInRow(0, '0 votes');
$I->seeInRow(1, '0 votes');

$I->clickInRow(1, "a[title='Up']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, $links[0]['title']);
$I->seeInRow(0, '1 votes');
$I->seeInRow(1, '0 votes');

$I->clickInRow(0, "a[title='Down']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, $links[1]['title']);
$I->seeInRow(1, $links[0]['title']);
$I->seeInRow(0, '0 votes');
$I->seeInRow(1, '0 votes');

$I->wantTo('try adding a comment');
$I->clickInRow(0, "a[title='Up']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, "1 votes");
$I->clickInRow(0, 'discuss');
$I->seeInCurrentUrl('link/view');
$I->see($links[1]['title']);
$I->see('1 votes');
$I->see('0 comments');
$I->fillField('CommentForm[comment]', 'test comment 1');
$I->click('Add Comment');
$I->wait(5);
$I->see('1 votes');
$I->see('1 comments');
$I->see('test comment 1');

$I->wantTo('check that list page updated');
$I->click('LinkoScope');
$I->seeInRow(0, '1 comments');
$I->seeInRow(0, '1 votes');

$I->wantTo('add a second comment');
$I->clickInRow(0, '1 comments');
$I->see('test comment 1');
$I->fillField('CommentForm[comment]', 'test comment 2');
$I->click('Add Comment');
$I->wait(5);
$I->see('1 votes');
$I->see('2 comments');
$I->see('test comment 1');
$I->see('test comment 2');

$I->wantTo('vote on comments');
$I->seeInRow(0, 'test comment 2');
$I->seeInRow(1, 'test comment 1');
$I->seeInRow(0, '0 votes');
$I->seeInRow(1, '0 votes');

$I->clickInRow(1, "a[title='Up']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, 'test comment 1');
$I->seeInRow(1, 'test comment 2');
$I->seeInRow(0, '1 votes');
$I->seeInRow(1, '0 votes');

$I->clickInRow(1, "a[title='Up']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, 'test comment 2');
$I->seeInRow(1, 'test comment 1');
$I->seeInRow(0, '1 votes');
$I->seeInRow(1, '1 votes');

$I->clickInRow(0, "a[title='Down']");
$I->wait(5);
$I->reloadPage();
$I->seeInRow(0, 'test comment 1');
$I->seeInRow(1, 'test comment 2');
$I->seeInRow(0, '1 votes');
$I->seeInRow(1, '0 votes');
