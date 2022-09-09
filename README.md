# php-identity
 
This PHP library is simplifying the authentication flows towards your Identity tenant.

## Usage
### Oauth 2.0 Authentication
The Identity object is responsible for handling all authentication flows against your tenant.
```php
$identity = new \Zploited\Identity\Client\Identity([
    'identifier' => 'tenant.auth.zploited.dk'   // (required) your identifier is the identifying subdomain url of your tenant.
    'client_id' => 'cINJBpkw2LoLDn6p1DwgfVcj2VpBhVt5' // (required) the ID of the client you are using.
    'client_secret' => 'LtjKmRKJ2h7UF745xvKC43QTlYj354zRaMzuwV7tOAMpbvmlSaNBpYW1SU6vNIP0' // (optional) if the client have a secret, it must be provided here.
    'redirect_uri' => 'https://domain.tld/callback' // (optional) the url used during authorization to return to your site.
    'scopes' = ['openid','email','documents:read'] // (optional) effective scopes we want to access.
]);
```
#### Authorization
To begin the authorization flow, you can use
```php
$identity->beginAuthorizationFlow(bool $implicit = false); // it takes a bool argument which states if this is an implicit grant or not.
```
This will redirect the browser to the authorization endpoint, and then when the authorization server redirects the user back to your site, using the redirect_uri, you will execute:
```php
$token = $identity->handleAuthorizationResponse();
```
which will handle the incoming response variables and if everything seems fine, it will provide you with a Token object.
#### Grants
Other than the authorization, you can also use the following, depending on what king of grant you want to use.
```php
$token = $identity->clientCredentials(); // if using this grant, the identity object is required to have the client_secret defined.
$token = $identity->password(string $email, string $password);
```