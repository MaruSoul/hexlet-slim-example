<?php

namespace Fakeldev\HexletSlimExample;

use Exception;

class UserRepository
{
    public function __construct()
    {
        session_start();
    }

    public function all()
    {
        return array_values($_SESSION);
    }

    public function find(?string $data)
    {
        $users = $this->all();

        $find = array_filter(
            $users,
            fn ($user) =>
            str_contains($user['nickname'], $data) ||
                str_contains($user['email'], $data) ||
                str_contains($user['id'], $data)
        );

        return $find;
    }

    public function save(array $item)
    {
        if (empty($item['nickname']) || $item['email'] === '') {
            $json = json_encode($item);
            throw new Exception("Wrong data: {$json}");
        }
        $item['id'] = uniqid();
        $_SESSION[$item['id']] = $item;
    }
}
