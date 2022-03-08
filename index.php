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
               
                $url = "https://api.themoviedb.org/3/movie/". $tmdbid ."?api_key=" . $api_key;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $rawdata = curl_exec($ch);
                curl_close($ch);
                $movieinfo = json_decode($rawdata,true);
                // print_r($movieinfo); exit(); 

                $url = "https://api.themoviedb.org/3/movie/". $tmdbid ."/credits?api_key=" . $api_key;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $rawdata_credits = curl_exec($ch);
                curl_close($ch);
                $credits = json_decode($rawdata_credits,true);
                
                $mijnoutput = '<div class="well">';
                $mijnoutput .= '<div class="well-img"><img src="https://www.themoviedb.org/t/p/w200/'.$movieinfo['poster_path'].'" alt="" class="img" width="200" height="300"></div>';
                $mijnoutput .= '<div class="well-body">';
                // $mijnoutput .= '<a href="https://www.imdb.com/title/'.$movieinfo['imdb_id'].'" class="floatright" title="Bekijken op IMDb">IMDb</a>';
                $mijnoutput .= '<p><a href="https://www.themoviedb.org/movie/'.$movieinfo['id'].'">'.$movieinfo['original_title']."</a><br>". $movieinfo['release_date']."</p>";
                $mijnoutput .= '<p><em>'.$movieinfo['tagline']."</em></p>";
                $mijnoutput .= '<p>'.mb_strimwidth($movieinfo['overview'],0,300, '&#8230;')."</p>";

                $i = 0;
                $mijnoutput .= "<ul class=\"cast\">";
                foreach ($credits['cast'] as $genre) {
                    $mijnoutput .= '<li>'. $genre['name'] . "</li>";
                    if (++$i == 5) break;
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= "<ul class=\"genres\">";
                foreach ($movieinfo['genres'] as $genre) {
                    $mijnoutput .= '<li>'. $genre['name'] . "</li>";
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= '</div></div>';
               
                return $mijnoutput;
            }
        ]
    ]
]);

?>