<?php
require_once('./models/middleware/Middleware.php');

class AuthModule extends Middleware{

    private function generate_header(): string{
        $headers = [
            'alg'=>'HS256',
            'typ'=>'JWT',
            'app'=>'gcccs-code-lab'
        ];

        return $this->base64url_encode(json_encode($headers));
    }

    public function generate_access_token($username, $role){

        $headers_encoded = $this->generate_header();

        //600s = 10min
        $payload = [
            'iss' => ISSUER,
            'aud' => AUDIENCE,
            'username' => $username,
            'role' => $role,
            'type' => 'access',
            'iat' => time(),
            'exp' => (time() + 600)
        ];

        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", SECRET, true);
        $signature_encoded = $this->base64url_encode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    public function generate_refresh_token($id)
    {
        $headers_encoded = $this->generate_header();

        //432000s = 5days
        $payload = [
            'iss' => ISSUER,
            'aud' => AUDIENCE,
            'id' => $id,
            'type' => 'refresh',
            'iat' => time(),
            'exp' => (time() + 432000)
        ];

        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", SECRET, true);
        $signature_encoded = $this->base64url_encode($signature);

        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }
}