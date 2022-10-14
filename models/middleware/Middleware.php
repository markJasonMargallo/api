<?php
class Middleware{
    /**
     * @throws Exception
     */
    public function authorize() {
        if (isset(getallheaders()["Authorization"])) {
            if (getallheaders()["Authorization"] !== 'null' and $this->is_authenticated(getallheaders()["Authorization"])) {
                return $this;
            }
        }
        throw new Exception('Unauthorized');
    }

    public function base64url_encode($str): string{
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public function is_authenticated($token){
        $token = $token;
        $tokenParts = explode('.', $token);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $issuer = json_decode($payload)->iss;
        $is_issuer_valid = ($issuer == ISSUER);
        $audience = json_decode($payload)->aud;
        $is_audience_valid = ($audience == AUDIENCE);

        $base64_url_header = $this->base64url_encode($header);
        $base64_url_payload = $this->base64url_encode($payload);

        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, SECRET, true);

        $base64_url_signature = $this->base64url_encode($signature);
        $is_signature_valid = ($base64_url_signature == $signature_provided);

        if ($is_token_expired || !$is_signature_valid || !$is_audience_valid || !$is_issuer_valid) {
            return false;
        }

        return true;

    }

    public function is_access_token($token)
    {
        $tokenParts = explode('.', $token);
        $payload = base64_decode($tokenParts[1]);

        $token_type = json_decode($payload)->type;


        if ($token_type !== 'access') {
            return false;
        }

        return true;
    }

    public function is_refresh_token($token)
    {
        $tokenParts = explode('.', $token);
        $payload = base64_decode($tokenParts[1]);

        $token_type = json_decode($payload)->type;

        if ($token_type !== 'refresh') {
            return false;
        }

        return true;
    }

    // public function is_instructor()
    // {
    //     $payload = base64_decode($this->tokenParts[1]);
    //     $role = json_decode($payload)->role;

    //     if ($role !== 'instructor') {
    //         return false;
    //     }

    //     return true;
    // }

    // public function is_student()
    // {
    //     $payload = base64_decode($this->tokenParts[1]);
    //     $role = json_decode($payload)->role;

    //     if ($role !== 'student') {
    //         return false;
    //     }
    //     return true;
    // }
}