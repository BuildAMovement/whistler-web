<?php
namespace ufw\helper;


/**
 * Dummy credentials storage.
 */
final class Credentials {
    // hash = password_hash($password, PASSWORD_DEFAULT);
    private static $hashes = [
        "admin" => '<hash>'
    ];

    public static function checkPassword(string $username, string $password) {
        if (empty($username)) { 
            return false;
        }

        return password_verify($password, Credentials::$hashes[strtolower($username)]);
    }
}
