# yii2-google-apiclient

A Yii2 wrapper for the official Google API PHP Client.

[![Latest Stable Version](https://poser.pugx.org/idk/yii2-google-apiclient/version)](https://packagist.org/packages/idk/yii2-google-apiclient)
[![Latest Unstable Version](https://poser.pugx.org/idk/yii2-google-apiclient/v/unstable)](//packagist.org/packages/idk/yii2-google-apiclient)
[![License](https://poser.pugx.org/idk/yii2-google-apiclient/license)](https://packagist.org/packages/idk/yii2-google-apiclient)
[![Total Downloads](https://poser.pugx.org/idk/yii2-google-apiclient/downloads)](https://packagist.org/packages/idk/yii2-google-apiclient)

This extension features:

* A console utility to generate your credentials files
* A component that will take care of the authentication, and give you access to the service

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer require idk/yii2-google-apiclient
```

## Configuration

**Credentials file**

In order to use this extension, you will be needing a credentials file for your Google Application.

You can generate this file using the provided console utility:

* Configure the module in `config/console.php`:
```php
'bootstrap' => ['log', 'yii2gac'],
'modules' => [
    'yii2gac' => [
        'class' => 'idk\yii2\google\apiclient\Module',
    ],
],
```

* Use the /configure sub command:
```shell 
./yii yii2gac/configure <clientSecretPath> [api]
```

where `clientSecretPath` is the path to your secret JSON file obtained from the [Google Console](https://console.developers.google.com/) and `api` the api identifier (it will be prompted for if not provided).


**Components**

You application may use as much Google_Service instances as you need, by adding an entry into the `components` index of the Yii configuration array.

Here's how to setup GMail for example, a usage sample is provided below.

```php
    'components' => [
        // ..
        'google' => [
            'class' => 'idk\yii2\google\apiclient\components\GoogleApiClient',
            'credentialsPath' => '@runtime/google-apiclient/auth.json',
            'clientSecretPath' => '@runtime/google-apiclient/secret.json',
        ],
```

This will enable you to access the GMail authenticated service `Yii::$app->google->getService()` in your application.

## Sample usage

**Displaying your newest message subject on GMail**

```php
$gmail = new Google_Service_Gmail(Yii::$app->google->getService());

$messages = $gmail->users_messages->listUsersMessages('me', [
    'maxResults' => 1,
    'labelIds' => 'INBOX',
]);
$list = $messages->getMessages();


if (count($list) == 0) {
    echo "You have no emails in your INBOX .. how did you achieve that ??";
} else {
    $messageId = $list[0]->getId(); // Grab first Message

    $message = $gmail->users_messages->get('me', $messageId, ['format' => 'full']);

    $messagePayload = $message->getPayload();
    $headers = $messagePayload->getHeaders();

    echo "Your last email subject is: ";
    foreach ($headers as $header) {
        if ($header->name == 'Subject') {
            echo "<b>" . $header->value . "</b>";
        }
    }

}
```
