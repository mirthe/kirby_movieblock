<?php Kirby::plugin('mirthe/movieblock', [
    'options' => [
        'cache' => true
    ],
    'tags' => [
        'movieblock' => [
            'attr' =>[
                'tmdb'
            ],
            'html' => function($tag) {
                $tmdbid = $tag->tmdb;
                $api_key = option('themoviedb.apiKey');

                $cache = kirby()->cache('mirthe.movieblock');
                $cacheKey = 'tmdb-' . $tmdbid;
                $movieData = $cache->get($cacheKey);

                if ($movieData === null) {
                    $movieUrl = "https://api.themoviedb.org/3/movie/" . $tmdbid . "?api_key=" . $api_key;
                    $ch = curl_init($movieUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
                    $rawMovie = curl_exec($ch);
                    curl_close($ch);

                    $creditsUrl = "https://api.themoviedb.org/3/movie/" . $tmdbid . "/credits?api_key=" . $api_key;
                    $ch = curl_init($creditsUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
                    $rawCredits = curl_exec($ch);
                    curl_close($ch);

                    $movieData = [
                        'movie'   => json_decode($rawMovie, true),
                        'credits' => json_decode($rawCredits, true)
                    ];

                    $cache->set($cacheKey, $movieData, 604800);
                }

                $movieinfo = $movieData['movie'] ?? null;
                $credits = $movieData['credits'] ?? null;

                if (empty($movieinfo) || !is_array($movieinfo) || isset($movieinfo['status_code'])) {
                    return '<div class="well"><div class="well-body">Film niet gevonden</div></div>';
                }

                $poster = isset($movieinfo['poster_path']) ? 'https://www.themoviedb.org/t/p/w200/'.$movieinfo['poster_path'] : '';
                $mijnoutput = '<div class="well">';
                if ($poster !== '') {
                    $mijnoutput .= '<div class="well-img"><img src="'.$poster.'" alt="" class="img" width="200" height="300"></div>';
                }
                $mijnoutput .= '<div class="well-body">';
                $mijnoutput .= '<p><a href="https://www.themoviedb.org/movie/'.$movieinfo['id'].'">'.($movieinfo['original_title'] ?? '')."</a><br>".($movieinfo['release_date'] ?? '')."</p>";
                $mijnoutput .= '<p><em>'.($movieinfo['tagline'] ?? '')."</em></p>";
                $mijnoutput .= '<p>'.mb_strimwidth($movieinfo['overview'] ?? '', 0, 300, '&#8230;')."</p>";

                $mijnoutput .= "<ul class=\"cast\">";
                if (!empty($credits['cast']) && is_array($credits['cast'])) {
                    $i = 0;
                    foreach ($credits['cast'] as $castMember) {
                        $mijnoutput .= '<li>'.($castMember['name'] ?? '')."</li>";
                        if (++$i === 5) break;
                    }
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= "<ul class=\"genres\">";
                if (!empty($movieinfo['genres']) && is_array($movieinfo['genres'])) {
                    foreach ($movieinfo['genres'] as $genre) {
                        $mijnoutput .= '<li>'.($genre['name'] ?? '')."</li>";
                    }
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= '</div></div>';
               
                return $mijnoutput;
            }
        ]
    ]
]);

?>