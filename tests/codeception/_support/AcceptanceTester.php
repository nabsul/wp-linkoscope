<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/

use ShortCirquit\LinkoScopeApi\iLinkoScope;
use Codeception\Scenario;

class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function __construct(Scenario $scenario, $type = null)
    {
        parent::__construct($scenario);
        if ($type == 'com' || $type == 'org')
            $this->configure($type);
    }

    public function getConfig()
    {
        $file = dirname(dirname(__DIR__)) . '/config.json';

        return json_decode(file_get_contents($file));
    }

    public function seeInRow($rowNumber, $text)
    {
        $this->see($text, "div[data-key='$rowNumber']");
    }

    public function dontSeeRow($rowNum)
    {
        $this->dontSeeElement("div[data-key='$rowNum']");
    }

    public function clickInRow($rowNum, $action)
    {
        $this->click($action, "div[data-key='$rowNum']");
        $this->wait(5);
    }

    public function addNewLink($title, $url)
    {
        $this->seeInCurrentUrl('link/index');
        $this->click('Add New');
        $this->see('Submit');
        $this->see('Title');
        $this->see('Url');

        $this->fillField('LinkForm[title]', $title);
        $this->fillField('LinkForm[url]', $url);
        $this->click('Submit');
        $this->wait(3);
    }

    public function login()
    {
        if ($this->getConfig()->type == 'com')
            $this->loginCom();
        else
            $this->loginOrg();
    }

    private function loginCom()
    {
        $this->click('Login');
        $this->seeInCurrentUrl('oauth2/authorize');
        $this->see('Howdy!');
        $this->see('Email or UserName');
        $this->see('Password');

        $this->fillField('log', $this->getConfig()->username);
        $this->fillField('pwd', $this->getConfig()->password);
        $this->click('button[type=submit]');

        $this->seeInCurrentUrl('oauth2/authorize');
        $this->see('Howdy!');
        $this->see('Deny');
        $this->see('Approve');
        $this->click('Approve');

        $this->seeInCurrentUrl('web/link');
    }

    private function loginOrg()
    {
        $this->wantTo('check that main page is working');
        $this->amOnPage('/');
        $this->see('LinkoScope');

        $this->click('Login');
        $this->wait(3);

        $this->seeInCurrentUrl('/wp-login.php');
        $this->see('Lost your password');
        $this->see('Back to');
        $this->see('UserName');
        $this->see('Password');

        $this->fillField('log', $this->getConfig()->username);
        $this->fillField('pwd', $this->getConfig()->password);
        $this->click('#wp-submit');
        $this->wait(3);

        $this->seeInCurrentUrl('/wp-login.php');
        $this->see('Howdy');
        $this->see('would like to connect to');
        $this->see('Authorize');
        $this->see('Cancel');
        $this->see('Switch user');
        $this->click('Authorize');
        $this->wait(3);

        $this->seeInCurrentUrl('link/index');
        $this->see('Shared Links:');
        $this->see('Add New');
    }

    public function deleteAllLinks()
    {
        /** @var iLinkoScope $api */
        $api = Yii::$app->linko->getConsoleApi();
        while(count($links = $api->getLinks()->links) > 0)
        {
            foreach ($links as $link)
                $api->deleteLink($link->id);
        }
    }

    public function setSiteData($count)
    {
        $this->deleteAllLinks();
        /** @var iLinkoScope $api */
        $api = Yii::$app->linko->getConsoleApi();

        $users = $api->getAccounts();
        $userCount = count($users);

        for ($i = 1; $i <= $count; $i++)
        {
            $link = new \ShortCirquit\LinkoScopeApi\Models\Link([
                'title' => "Test Title #$i",
                'url' => "http://url$i.com/$i/$i",
                'authorId' => $users[$i % $userCount]->id,
            ]);
            $api->addLink($link);
        }
    }

    private function configure($type){
        $output = dirname(dirname(dirname(__DIR__))) . '/runtime/config.json';
        $input = dirname(dirname(__DIR__)) . '/config.json';
        $cfg = json_decode(file_get_contents($input));
        file_put_contents($output, json_encode($cfg->$type, JSON_PRETTY_PRINT));
        new yii\web\Application(require(dirname(__DIR__) . '/config/acceptance.php'));
    }

    public function getAdminPass(){
        return trim(file_get_contents(Yii::$app->runtimePath . '/adminPass.txt'));
    }
}
