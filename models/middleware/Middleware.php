<?php
require_once('./models/Token.php');
require_once('./enums/TokenTypes.php');
require_once('./enums/RoleTypes.php');

class Middleware
{
    private Token $token;

    public function __construct($token)
    {
        $this->token = new Token(extracted_token: $token);
    }

    public function is_token_valid()
    {
        return $this->token->is_token_valid();
    }

    public function is_access_token()
    {
        return $this->token->get_payload()->type == 'access';
    }

    public function is_refresh_token()
    {
        return $this->token->get_payload()->type == 'refresh';
    }

    public function is_instructor()
    {
        return $this->token->get_payload()->role == 'instructor';
    }

    public function is_student()
    {
        return $this->token->get_payload()->role == 'student';
    }

    public function get_id_from_token()
    {
        return $this->token->get_payload()->id;
    }

    public function get_owner_email()
    {
        return $this->token->get_owner_email();
    }
}
