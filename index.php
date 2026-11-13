<?php

require_once __DIR__ . '/src/tmdb.php';
require_once __DIR__ . '/src/render.php';

Kirby::plugin('mirthe/movieblock', [
    'options' => [
        'apiKey' => option('themoviedb.apiKey'),
        'cache' => true
    ],
    'translations' => [
        'nl' => [
            'mirthe.movieblock.not-found' => 'Film niet gevonden'
        ],
        'en' => [
            'mirthe.movieblock.not-found' => 'Film not found'
        ]
    ],
    'tags' => [
        'movieblock' => [
            'attr' =>[
                'tmdb'
            ],
            'html' => function($tag) {
                return mirthe_movieblock_render((string) $tag->tmdb);
            }
        ]
    ]
]);

?>