<?php
require_once('./models/Token.php');
require_once('./enums/TokenTypes.php');
require_once('./enums/RoleTypes.php');

class AuthModule{

    private Token $token;

    public function generate_access_token($email, $id, RoleTypes $role){

        $this->token = new Token(email: $email, id: $id, role: $role);
        return $this->token->get_token();

    }

    public function generate_refresh_token($id){
        
        $this->token = new Token(id: $id, type: TokenTypes::REFRESH, expiration: 432000);
        return $this->token->get_token();
        
    }
}