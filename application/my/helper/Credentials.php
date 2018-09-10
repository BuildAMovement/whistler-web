<?php
namespace helper;


final class Credentials {
    public static function checkPassword(string $username, string $password) {
        return \ufw\helper\Credentials::checkPassword($username, $password);
    }
}
