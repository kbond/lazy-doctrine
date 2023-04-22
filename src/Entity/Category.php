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

    public const ALL = [
        self::BOOKS,
        self::ELECTRONICS,
        self::MOVIES,
        self::MUSIC,
        self::TOYS,
    ];
}
