<?php
/*
* Token.php
* CSRF token implemeatation (requires session)
*/

class Token{

    /**
    * Generates a token
    * @return string
    */
    public static function generate()
    {
        // Generate token
        $token = bin2hex(openssl_random_pseudo_bytes(64));
        $_SESSION['csrf_token'] = $token;

        // Return token
        return $token;
    }


    /**
    * Verifies token (Kills app if invalid)
    */
    public static function verify()
    {
        // Check for POST request
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token'])
            {
                // Kill the application
                die('Invalid Token');
            }
        }
    } 
}