<?php

/**
 * Class token for protection to CSRF
 */
class Token {

    private $token;

    function __construct() {
        $this->token = $this->generate_token();
    }

    public function display_token() {
        return $this->token;
    }

    private function generate_token() {
        return bin2hex(random_bytes(32));
    }

}
