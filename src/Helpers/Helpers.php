<?php

namespace App\Helpers;

class Helpers
{

    public static function getSortList(): array
    {
        return [
            'id_asc' => 'id ASC',
            'id_desc' => 'id DESC',
            'title_asc' => 'title ASC',
            'title_desc' => 'title DESC',
            'content_asc' => 'content ASC',
            'content_desc' => 'content DESC',
        ];
    }

    public static function getColumnTitle(): array
    {
        return [
            1 => ['id' => 'id', 'title' => '№'],
            2 => ['id' => 'title', 'title' => 'Название'],
            3 => ['id' => 'content', 'title' => 'Контент'],
        ];
    }
}