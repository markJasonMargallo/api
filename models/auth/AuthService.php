<?php
require_once('./modules/Auth.php');
require_once('./models/auth/AuthRepository.php');
require_once('./modules/Procedural.php');
require_once('./vendor/autoload.php');

class AuthService
{
    private AuthModule $auth_module;
    private AuthRepository $auth_repository;

    public function __construct()
    {
        $this->auth_module = new AuthModule();
        $this->auth_repository = new AuthRepository();
    }

    // public function register_student($data)
    // {
    //     if (
    //         $data->hei_id &&
    //         $data->username &&
    //         $data->email_address &&
    //         $data->password
    //     ) {
    //         $hei = $this->hei_repository->get_hei_by_hei_id($data->hei_id);
    //         $user = $this->auth_repository->get_user_by_hei_id($data->hei_id);

    //         if ($user) {
    //             return response(['message' => 'There can only be one user per HEI'], 400);
    //         }

    //         if (!$hei) {
    //             return response(['message' => 'Invalid hei id'], 400);
    //         }

    //         $last_insert_id = $this->auth_repository->register_hei($data);

    //         if ($last_insert_id['count']) {
    //             $payload = [
    //                 "id" => $last_insert_id['last_id'],
    //                 "email_address" => $data->email_address,
    //             ];

    //             $code = 200;

    //             return response($payload, $code);
    //         }
    //     } else {
    //         return response(['message' => 'Invalid request body'], 400);
    //     }
    // }

    public function login_student($credentials): array
    {

        // Validate
        $validator = new JsonSchema\Validator;
        $validator->validate($credentials, (object)['$ref' => 'file://' . realpath('credentials_schema.json')]);

        if ($validator->isValid()) {
            echo "The supplied JSON validates against the schema.\n";
        } else {
            echo "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                printf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        if ($credentials->username && $credentials->password) {
            $user = $this->auth_repository->get_student_account_by_username($credentials->username);

            if (!$user) {

                return response(['message' => 'User not found'], 404);
            }

            // $verified_password = password_verify($credentials->password, $user['password']);
            echo json_encode($user);
            if ($credentials->password !== $user['password']) {
                return response(['message' => 'Invalid credentials'], 401);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], $user['role']);

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
        if ($credentials->username && $credentials->password) {
            $user = $this->auth_repository->get_instructor_account_by_username($credentials->username);


            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            // $verified_password = password_verify($credentials->password, $user['password']);

            if ($credentials->password !== $user['password']) {
                return response(['message' => 'Invalid credentials'], 401);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], $user['role']);

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

            $user = $this->auth_repository->get_account_by_id($credential_id);

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['username'], $user['role']);

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
