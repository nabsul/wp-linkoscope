<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 2015-09-17
 * Time: 11:14 AM
 */

namespace automattic\Yii;

use yii\base\Object;

/**
 * Class YiiWpApi
 * @package automattic\Yii
 */
class WpApi extends Object {
	public $configPath = '@runtime';
	public $configFileName = 'api.cfg';

	public function getRestApi() {

	}
}