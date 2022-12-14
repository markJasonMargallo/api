<?php

require_once('./modules/AuthModule.php');
require_once('./models/auth/AuthRepository.php');
require_once('./modules/Procedural.php');
require_once('./modules/Validation.php');
require_once('./enums/RoleTypes.php');
require_once('./modules/CookieModule.php');
require_once('./models/Token.php');

class AuthService
{
    private AuthModule $auth_module;
    private AuthRepository $auth_repository;
    private Validation $validator;

    public function __construct()
    {
        $this->auth_module = new AuthModule();
        $this->auth_repository = new AuthRepository();
        $this->validator = new Validation();
    }

    public function get_instructor_id($user_email)
    {
        return $this->auth_repository->get_instructor($user_email)['instructor_id'];
    }

    public function register_student($data)
    {
        $student_data = $data->student_data;
        $account_data = $data->account_data;
        if ($this->validator->is_body_valid($student_data, './schemas/student_schema.json') && $this->validator->is_body_valid($account_data, './schemas/credential_schema.json')) {

            if ($this->auth_repository->get_account($account_data->email)) {
                return response(['message' => 'Email already exist'], 400);
            }

            $account_id = $this->auth_repository->add_account($account_data, 'student');
            if ($this->auth_repository->add_student($student_data, $account_id) > 0) {
                return response(['message' => 'Student registration successful'], 200);
            }
        } else {
            return $this->validator->invalid_body();
        }
    }

    public function register_instructor($data)
    {
        $instructor_data = $data->instructor_data;
        $account_data = $data->account_data;

        if ($this->validator->is_body_valid($instructor_data, './schemas/instructor_schema.json') && $this->validator->is_body_valid($account_data, './schemas/credential_schema.json')) {

            if ($this->auth_repository->get_account($account_data->email)) {
                return response(['message' => 'Email already exist'], 400);
            }

            $account_id = $this->auth_repository->add_account($account_data, 'instructor');

            if ($this->auth_repository->add_instructor($instructor_data, $account_id) > 0) {
                return response(['message' => 'Instructor registration successful'], 200);
            }
        } else {
            return $this->validator->invalid_body();
        }
    }

    public function login_student($credentials): array
    {
        if ($this->validator->is_body_valid($credentials, './schemas/credential_schema.json')) {

            $user = $this->auth_repository->get_student_account($credentials->email);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            $verified_password = password_verify($credentials->password, $user['password']);

            if (!$verified_password) {
                return response(['message' => 'Invalid credentials'], 403);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['student_id']);
            $access_token = $this->auth_module->generate_access_token($user['email'], $user['student_id'], RoleTypes::STUDENT);

            //set http-only cookie
            $cookie_module = new CookieModule($refresh_token);
            $cookie_module->set_cookie();

            //set Authorization header
            header('Authorization: ' . $access_token);
            http_response_code(200);

            $student = $this->auth_repository->get_student_by_account($user['id']);

            return response($student, 200);
        } else {
            return $this->validator->invalid_body();
        }
    }

    public function login_instructor($credentials): array
    {
        if ($this->validator->is_body_valid($credentials, './schemas/credential_schema.json')) {

            $user = $this->auth_repository->get_instructor_account($credentials->email);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            $verified_password = password_verify($credentials->password, $user['password']);

            if (!$verified_password) {
                return response(['message' => 'Invalid credentials'], 401);
            }

            $refresh_token = $this->auth_module->generate_refresh_token($user['instructor_id']);
            $access_token = $this->auth_module->generate_access_token($user['email'], $user['instructor_id'], RoleTypes::INSTRUCTOR);

            //set http-only cookie
            $cookie_module = new CookieModule($refresh_token);
            $cookie_module->set_cookie();

            //set Authorization header
            header('Authorization: ' . $access_token);
            http_response_code(200);

            $instructor = $this->auth_repository->get_instructor_by_account($user['id']);

            return response($instructor, 200);
        } else {
            return $this->validator->invalid_body();
        }
    }

    function refresh_token()
    {
        if (isset($_COOKIE['_tr_uc'])) {

            $refresh_token = new Token(extracted_token: $_COOKIE['_tr_uc']);

            if (!$refresh_token->is_refresh_token()) {
                throw new Exception('Invalid token type');
            }

            $user = $this->auth_repository->get_account_by_id($refresh_token->get_owner_id());
            $user_role = ($user['role'] == 'student') ? RoleTypes::STUDENT : RoleTypes::INSTRUCTOR;

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['email'], $user['id'], $user_role);

            //set Authorization header
            header('Authorization: ' . $access_token);
            http_response_code(200);

            return response(['message' => 'Logged in'], 200);
        } else {
            return response(['message' => 'Invalid refresh token'], 200);
        }
    }

    function instructor_refresh_token()
    {
        if (isset($_COOKIE['_tr_uc'])) {

            $refresh_token = new Token(extracted_token: $_COOKIE['_tr_uc']);

            if (!$refresh_token->is_refresh_token()) {
                throw new Exception('Invalid token type');
            }

            $user = $this->auth_repository->get_account_by_id($refresh_token->get_owner_id());
            $user_role = ($user['role'] == 'student') ? RoleTypes::STUDENT : RoleTypes::INSTRUCTOR;

            $refresh_token = $this->auth_module->generate_refresh_token($user['id']);
            $access_token = $this->auth_module->generate_access_token($user['email'], $user['id'], $user_role);

            //set Authorization header
            header('Authorization: ' . $access_token);
            http_response_code(200);

            return response(['message' => 'Logged in'], 200);
        } else {
            return response(['message' => 'Invalid refresh token'], 200);
        }
    }
}
