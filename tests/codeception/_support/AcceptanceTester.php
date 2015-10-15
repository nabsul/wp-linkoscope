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
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */


    public $secrets;

    public function loadSecrets()
    {
        $file = __DIR__ . '/../config/secrets.json';
        if (!file_exists($file))
            throw new Exception('no secrets file found');
        $this->secrets = json_decode(file_get_contents($file));
    }

    public function seeInRow($rowNumber, $text){
        $this->see($text, "div[data-key='$rowNumber']");
    }

    public function dontSeeRow($rowNum){
        $this->dontSeeElement("div[data-key='$rowNum']");
    }

    public function clickRowAction($rowNum, $action){
        $this->click("a[title='$action']", "div[data-key='$rowNum']");
        $this->wait(5);
    }

    public function addNewLink($title, $url){
        $this->seeInCurrentUrl('link/index');
        $this->click('Add New');
        $this->see('Submit Link');
        $this->see('Title');
        $this->see('Url');

        $this->fillField('LinkForm[title]', $title);
        $this->fillField('LinkForm[url]', $url);
        $this->click('Submit');
        $this->wait(3);
    }
}
