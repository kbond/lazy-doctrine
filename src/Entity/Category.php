<?php

namespace App\Entity;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
enum Category : string
{
    case BOOKS = 'books';
    case ELECTRONICS = 'electronics';
    case MOVIES = 'movies';
    case MUSIC = 'music';
    case TOYS = 'toys';

    public static function values(): array
    {
        return \array_column(self::cases(), 'value');
    }

    public static function random(): self
    {
        return self::cases()[\array_rand(self::cases())];
    }
}
