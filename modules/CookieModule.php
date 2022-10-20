<?php
class CookieModule
{
    private $cookie_options;
    private $refresh_token;

    public function __construct($refresh_token)
    {

        // TODO: set secure to true when deploying
        // in development, requests are sent through HTTP so setting the secure flag to true, won't work.

        // 432000 seconds = 5 days

        $this->cookie_options = array(
            'expires' => time() + 432000,
            'path' => '/',
            'httponly' => true, // or false
            'secure' => false, // or false
            'samesite' => 'Lax' // None || Lax || Strict
        );

        $this->refresh_token = $refresh_token;


    }

    public function set_cookie(){
        setcookie("_tr_uc", $this->refresh_token, $this->cookie_options);
    }
}
