<?php
use ShortCirquit\LinkoScopeApi\iLinkoScope;
/* @var $I AcceptanceTester */

/* @var $api iLinkoScope */
$api = Yii::$app->linko->getConsoleApi();

$link = new \ShortCirquit\LinkoScopeApi\Models\Link();
$link->title = 'comment pagination test';
$link->url = 'http://bing.com';
$link = $api->addLink($link);
$user = $api->getAccount();

for ($i = 0; $i < 10; $i++)
{
    $comment = new \ShortCirquit\LinkoScopeApi\Models\Comment();
    $comment->content = "Comment #$i";
    $comment->postId = $link->id;
    $comment->authorId = $user->id;
    $comment->authorName = $user->name;
    $api->addComment($comment);
}

$I->amOnPage('/');
$I->login();

$I->amOnPage("/link/view?id={$link->id}&pageSize=2");
$I->seeInRow(0, 'Comment #9');
$I->seeInRow(1, 'Comment #8');
$I->dontSeeRow(2);

$I->click("a[data-page='1']");
$I->seeInRow(0, 'Comment #7');
$I->seeInRow(1, 'Comment #6');
$I->dontSeeRow(2);

$I->click("a[data-page='2']");
$I->seeInRow(0, 'Comment #5');
$I->seeInRow(1, 'Comment #4');
$I->dontSeeRow(2);

