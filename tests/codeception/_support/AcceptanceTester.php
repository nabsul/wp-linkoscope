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

    public function getConfig()
    {
        $file = __DIR__ . '/../../config.json';
        return json_decode(file_get_contents($file));
    }

    public function seeInRow($rowNumber, $text){
        $this->see($text, "div[data-key='$rowNumber']");
    }

    public function dontSeeRow($rowNum){
        $this->dontSeeElement("div[data-key='$rowNum']");
    }

    public function clickInRow($rowNum, $action){
        $this->click($action, "div[data-key='$rowNum']");
        $this->wait(5);
    }

    public function addNewLink($title, $url){
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
}
