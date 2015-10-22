<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\Object;

/**
 * Class User
 * @package app\models
 *
 * @param adminAccount User
 */
class User extends Object implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $token;
    public $secret;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        foreach (self::getBothAccounts() as $acc)
        {
            if ($acc != null && $acc->id == $id)
            {
                return $acc;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $accounts = self::getBothAccounts();
        foreach ($accounts as $acc)
        {
            if ($acc != null && $acc->username == $username)
            {
                return $acc;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public static function adminConfigured()
    {
        return User::getAdminAccount() != null;
    }


    private static function getAdminAccount()
    {
        $file = Yii::$app->runtimePath . '/adminPass.txt';
        if (!file_exists($file)){
            return null;
        }

        $pass = trim(file_get_contents($file));
        return new User([
            'id' => 'admin',
            'username' => 'admin',
            'password' => $pass,
        ]);
    }

    private static function getSessionAccount()
    {
        $session = Yii::$app->session;
        if (!$session->has('wp_rest_user'))
            return null;

        return new User([
            'id' => $session->get('wp_rest_id'),
            'username' => $session->get('wp_rest_user'),
            'token' => $session->get('wp_rest_token'),
            'secret' => $session->get('wp_rest_secret'),
        ]);
    }

    public function saveSessionAccount()
    {
        $session = Yii::$app->session;
        $session->set('wp_rest_id', $this->id);
        $session->set('wp_rest_user', $this->username);
        $session->set('wp_rest_token', $this->token);
        $session->set('wp_rest_secret', $this->secret);
    }

    private static function getBothAccounts()
    {
        return [self::getAdminAccount(), self::getSessionAccount()];
    }
}
