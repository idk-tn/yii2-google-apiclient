<?php

namespace idk\yii2\google\apiclient\components;

use Google_Client;
use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class GoogleApiClient
 *
 * @package idk\yii2\google\apiclient
 */
class GoogleApiClient extends Component
{
    /**
     * @var string Your application name
     */
    public $applicationName = 'My Application';

    /**
     * @var string The credential file path
     */
    public $credentialsPath;

    /**
     * @var string The client secret file path
     */
    public $clientSecretPath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->clientSecretPath = Yii::getAlias($this->clientSecretPath);
        if (!file_exists($this->clientSecretPath)) {
            throw new Exception("The client secret file \"{$this->clientSecretPath}\" does not exist!");
        }

        $this->credentialsPath = Yii::getAlias($this->credentialsPath);
        if (!file_exists($this->credentialsPath)) {
            throw new Exception("The credential file \"{$this->credentialsPath}\" does not exist!");
        }
    }

    /**
     * Gets a Google Service
     *
     * @return Google_Client the authorized client object
     * @throws Exception
     */
    public function getService()
    {
        return $this->getClient($this->getAccessToken());
    }

    /**
     * Returns an authorized API client.
     *
     * @param $accessToken string the access token
     * @return Google_Client the authorized client object
     */
    public function getClient($accessToken)
    {
        $client = new Google_Client();
        $client->setApplicationName($this->applicationName);
        $client->setAccessType('offline');

        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->setAuthConfigFile(Yii::getAlias($this->clientSecretPath));
            $client->refreshToken($client->getRefreshToken());
            $this->saveAccessToken($client->getAccessToken());
        }
        return $client;
    }

    /**
     * Writes down the access token in the credentials file.
     *
     * @param $access_token string the access token
     */
    private function saveAccessToken($access_token)
    {
        file_put_contents($this->credentialsPath, $access_token);
    }

    /**
     * Reads the access token from the credentials file
     *
     * @return string the access token
     */
    private function getAccessToken()
    {
        return file_get_contents($this->credentialsPath);
    }
}
