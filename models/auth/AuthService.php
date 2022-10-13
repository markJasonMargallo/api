<?php
class AuthService
{
    private AuthModule $auth_module;

    public function __construct()
    {
        $this->auth_module = new AuthModule();
    }

    public function login_student($credentials): array
    {
        if ($credentials->email_address && $credentials->password) {
            $user = $this->auth_repository->get_user_by_email_address($credentials->email_address);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            $verified_password = password_verify($credentials->password, $user['password']);

            if (!$verified_password) {
                return response(['message' => 'Invalid credentials'], 401);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], 'student');

            // 432000s = 5 days
            $cookie_options = $this->get_cookie_options();

            setcookie("_tr_uc", $refresh_token, $cookie_options);
            header('Authorization: ' . $access_token);
            http_response_code(200);
            return response(['message' => 'Logged in'], 200);
        }

        return response(['message' => 'Invalid request body'], 403);
    }

    public function login_instructor($credentials): array
    {
        if ($credentials->email_address && $credentials->password) {
            $user = $this->auth_repository->get_user_by_email_address($credentials->email_address);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            $verified_password = password_verify($credentials->password, $user['password']);

            if (!$verified_password) {
                return response(['message' => 'Invalid credentials'], 401);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], 'instructor');

            // 432000s = 5 days
            $cookie_options = $this->get_cookie_options();

            setcookie("_tr_uc", $refresh_token, $cookie_options);
            header('Authorization: ' . $access_token);
            http_response_code(200);
            return response(['message' => 'Logged in'], 200);
        }

        return response(['message' => 'Invalid request body'], 403);
    }

    function refresh_token()
    {
        $refresh_token = $_COOKIE['_tr_uc'];

        if (isset($refresh_token)) {

            if (!$this->auth_module->is_refresh_token($refresh_token)) {
                throw new Exception('Invalid token type');
            }

            if (!$this->auth_module->is_authenticated($refresh_token)) {
                throw new Exception('Invalid token');
            }

            $tokenParts = explode('.', $refresh_token);
            $payload = base64_decode($tokenParts[1]);
            $credential_id = json_decode($payload)->id;

            $user = $this->auth_repository->get_user_by_credential_id($credential_id);

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], $user['rolecode']);

            $cookie_options = $this->get_cookie_options();

            setcookie("_tr_uc", $refresh_token, $cookie_options);
            header('Authorization: ' . $access_token);
            http_response_code(200);
            return response(['message' => 'Logged in'], 200);
        } else {
            return response(['message' => 'Invalid refresh token'], 400);
        }
    }

    private function get_cookie_options()
    {
        //TODO: set secure to true when deploying
        // in development, requests are sent through HTTP so settting the secure flag to true,
        // won't work.

        // 432000s = 5 days
        return array(
            'expires' => time() + 432000,
            'path' => '/',
            'httponly' => true, // or false
            'secure' => false, // or false
            'samesite' => 'Lax' // None || Lax || Strict
        );
    }
}
