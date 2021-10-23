<?php

namespace Fakeldev\HexletSlimExample;

class Validator
{
    public function validate(array $user)
    {
        // BEGIN (write your solution here)
        $errors = [];
        if (empty($user['nickname'])) {
            $errors = 'nickname';
        }

        if (empty($user['email'])) {
            $errors = 'email';
        }


        if (empty($errors)) {
            return true;
        } else {
            echo 'Can\'t be blank';
            return false;
        }
        // END
    }
}
