<?php
require_once('./enums/TokenTypes.php');
require_once('./enums/RoleTypes.php');
require_once('./modules/Validation.php');
require_once('./models/exception/AuthorizationException.php');

class Token
{

    private $header;
    private $payload;
    private $signature;
    private Validation $validator;
    private $encoded_header;
    private $encoded_payload;

    /**
     * @param  string $email email address of the user
     * @param  integer $id account id address of the user. Default = 0
     * @param  string $role role address of the user. Default = 'student'
     * @param  string $type type of token. Default = 'access'
     * @param  integer $expiration type of the token. Default = 600s (10 mins)
     * @param  string $extracted_token token attached to the current request. Default = 'null'
     */
    
    public function __construct($email = null, $id = null, RoleTypes $role = RoleTypes::STUDENT, TokenTypes $type = TokenTypes::ACCESS, $expiration = 86400, $extracted_token = null)
    {

        $this->validator = new Validation();

        if ($type == TokenTypes::ACCESS) {
            $payload = [
                'iss' => ISSUER,
                'aud' => AUDIENCE,
                'email' => $email,
                'id' => $id,
                'role' => ($role == RoleTypes::STUDENT) ? 'student' : 'instructor',
                'type' => 'access',
                'iat' => time(),
                'exp' => (time() + $expiration)
            ];
        } else {
            $payload = [
                'iss' => ISSUER,
                'aud' => AUDIENCE,
                'id' => $id,
                'type' => 'refresh',
                'iat' => time(),
                'exp' => (time() + $expiration)
            ];
        }

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
            'app' => 'gcccs-codelab'
        ];

        if (is_null($extracted_token)) {

            $this->payload = $this->base64url_encode(json_encode($payload)); //tama
            $this->header = $this->base64url_encode(json_encode($header)); //tama

            $signature = hash_hmac('SHA256', "$this->header.$this->payload", SECRET, true);

            $this->signature = $this->base64url_encode($signature); //tama

        } else {

            $token = str_replace('Bearer', '', $extracted_token);

            

            $tokenParts = explode('.', $token);

            $extracted_header = base64_decode($tokenParts[0]);
            $extracted_payload = base64_decode($tokenParts[1]);
            $extracted_signature = $tokenParts[2];

            $this->encoded_header = $tokenParts[0];
            $this->encoded_payload = $tokenParts[1];
            $this->payload = $extracted_payload;
            $this->header = $extracted_header;
            $this->signature = $extracted_signature;

            $extracted_token_type = ($this->get_token_type() == 'access') ? TokenTypes::ACCESS : TokenTypes::REFRESH;

            if ($extracted_token_type == TokenTypes::ACCESS) {

                // check if access token payload is valid
                if (!$this->validator->is_body_valid($this->get_payload(), './schemas/access_token_payload_schema.json')) {
                    // $this->validator->invalid_body();
                    throw new AuthenticationException();
                }
            } elseif ($extracted_token_type == TokenTypes::REFRESH) {

                // check if refresh token payload is valid
                if (!$this->validator->is_body_valid($this->get_payload(), './schemas/refresh_token_payload_schema.json')) {
                    // $this->validator->invalid_body();
                    throw new AuthenticationException();
                }
            }

            // check if token is valid
            if (!$this->is_token_valid()) {
                throw new AuthorizationException();
            }
        }
    }

    private function base64url_encode($str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public function get_header()
    {
        return json_decode($this->header);
    }

    public function get_payload()
    {
        return json_decode($this->payload);
    }

    public function get_token_type()
    {
        return json_decode($this->payload)->type;
    }

    public function get_signature()
    {
        return $this->signature;
    }

    public function get_token(): string
    {
        return "$this->header.$this->payload.$this->signature";
    }

    public function is_token_valid()
    {

        $extracted_payload = $this->get_payload();
        $signature_provided = $this->get_signature();
        $signature_resigned = hash_hmac('SHA256', $this->encoded_header . "." . $this->encoded_payload, SECRET, true);

        $is_token_expired = ($extracted_payload->exp - time()) < 0;
        $is_issuer_valid = ($extracted_payload->iss == ISSUER);
        $is_audience_valid = ($extracted_payload->aud == AUDIENCE);

        $is_signature_valid = ($this->base64url_encode($signature_resigned) == $signature_provided);

        if ($is_token_expired || !$is_signature_valid || !$is_audience_valid || !$is_issuer_valid) {
            return false;
        }

        return true;
    }

    public function is_access_token()
    {
        return $this->get_payload()->type == 'access';
    }

    public function is_refresh_token()
    {
        return $this->get_payload()->type == 'refresh';
    }

    public function is_instructor()
    {
        return $this->get_payload()->role == 'instructor';
    }

    public function is_student()
    {
        return $this->get_payload()->role == 'student';
    }

    public function get_owner_id()
    {
        return $this->get_payload()->id;
    }

    public function get_owner_email()
    {
        return $this->get_payload()->email;
    }

    public function get_student_id(){
        
    }
}
