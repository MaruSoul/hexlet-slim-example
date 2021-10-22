<?php

namespace Fakeldev\HexletSlimExample;

use Exception;

class CourseRepository
{
    public function __construct()
    {
        session_start();
    }

    public function all()
    {
        return array_values($_SESSION);
    }

    public function find(int $id)
    {
        if (!isset($_SESSION[$id])) {
            throw new Exception("Wrong course id: {$id}");
        }

        return $_SESSION[$id];
    }

    public function save(array $item)
    {
        if (empty($item['nickname']) || $item['email'] === '' ||  $item['id'] === '') {
            $json = json_encode($item);
            throw new Exception("Wrong data: {$json}");
        }
    }
}
