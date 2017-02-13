# OAuth 2.0  - YouTube Provider
A YouTube OAuth2 provider for [league/oauth2-client](https://github.com/thephpleague/oauth2-client). 

Used to get basic information about a YouTube channel (channel name, profile image, etc).

## Installation
Install this package using composer:
```
composer require mitchwilliamson/oauth2-youtube
```

## Usage
```php
$provider = new \League\OAuth2\Client\Provider\YouTube([
    'clientId'                => '{youtube-client-id}',    // The client ID assigned to you by Google/YouTube
    'clientSecret'            => '{youtube-client-secret}',   // The client secret assigned to you by Google/YouTube
    'redirectUri'             => 'http://example.com/your-redirect-url/',
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    try {

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $request->input('code')
        ]);
        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);

        var_export($resourceOwner->toArray());

        // You can also use these functions:
        $channelId = $resourceOwner->getId();
        $channelName = $resourceOwner->getName();
        $channelAvatar = $resourceOwner->getImageurl();
        $channelDescription = $resourceOwner->getDescription();
        
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}
```

## Contributing
This package is very basic at the moment. Any contributions are welcome via a pull request! :)

## Todo
* Handle exceptions
* Provide more functionality

## Credits
* [Mitchell Williamson](https://github.com/mitchwilliamson)


## License
MIT License (MIT), (see LICENSE file for details).