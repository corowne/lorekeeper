<?php namespace App\Services;

use App\Services\Service;

use chillerlan\HTTP\Psr7;
use chillerlan\OAuth\Providers\DeviantArt\DeviantArt;

use chillerlan\OAuth\Core\AccessToken;
use chillerlan\HTTP\Psr18\CurlClient;
use chillerlan\OAuth\{OAuthOptions, Storage\SessionStorage};
use chillerlan\DotEnv\DotEnv;

class DeviantArtService extends Service
{
    public function beforeConstruct() {
        /** @var \chillerlan\OAuth\Providers\DeviantArt\DeviantArt $deviantart */
        $this->deviantart  = null;
        $this->servicename = null;
        $this->tokenfile   = null;
    
        /** @var \chillerlan\Settings\SettingsContainerInterface $options */
        $this->options = null;
        /** @var \chillerlan\HTTP\Psr18\HTTPClientInterface $http */
        $this->http = null;
        /** @var \chillerlan\OAuth\Storage\OAuthStorageInterface $storage */
        $this->storage = null;

        $this->options_arr = [
            // OAuthOptions
            'key'              => env('DEVIANTART_CLIENT_ID'),
            'secret'           => env('DEVIANTART_CLIENT_SECRET'),
            'callbackURL'      => url('link'), 
            'tokenAutoRefresh' => true,
            // HTTPOptions
            'ca_info'          => public_path().'/cacert.pem',
            'userAgent'        => 'chillerlanPhpOAuth/3.0.0 +https://github.com/codemasher/php-oauth',
            // log
            'minLogLevel'      => 'debug',
        ];
        /** @var \chillerlan\Settings\SettingsContainerInterface $options */
        $this->options = new class($this->options_arr) extends OAuthOptions{
            protected $sleep;
        };
        
        /** @var \chillerlan\HTTP\Psr18\HTTPClientInterface $http */
        $this->http = new CurlClient($this->options);

        /** @var \chillerlan\OAuth\Storage\OAuthStorageInterface $storage */
        $this->storage = new SessionStorage;

        $this->deviantart = new DeviantArt($this->http, $this->storage, $this->options, null);

        $this->scopes = [
            DeviantArt::SCOPE_BASIC,
            DeviantArt::SCOPE_BROWSE,
        ];
        $this->servicename = $this->deviantart->serviceName;
    }
    
    /**
     * Get the Auth URL for dA
     */
    public function getAuthURL() {
        return $this->deviantart->getAuthURL(null, $this->scopes);
    }

    /**
     * Get the access token
     */
    public function getAccessToken($code, $state) {
        
        $token = $this->deviantart->getAccessToken($code, $state);

        // The token can be saved for continued use, but at the moment it's not needed more than this once.
    }

    /**
     * Link the user's deviantART name to their account
     */
    public function linkUser($user) {
        $data = Psr7\get_json($this->deviantart->whoami());

        // Save the user's username
        // Also consider: save the user's dA join date
        $user->alias = $data->username;
        $user->save();
    }
}