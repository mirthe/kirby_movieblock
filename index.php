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
                // return "Dingen ophalen voor ". $filmtitel;

                $api_key = option('themoviedb.apiKey');
               
                $url = "https://api.themoviedb.org/3/movie/". $tmdbid ."?api_key=" . $api_key;
                // $headers = array(
                //     'x-api-key: '.$api_key, 
                //     'Accept: application/json');
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $rawdata = curl_exec($ch);
                curl_close($ch);
                
                $movieinfo = json_decode($rawdata,true);
                // print_r($movieinfo); exit(); 
                
                $mijnoutput = '<div class="well" style="overflow: auto;">';
                $mijnoutput .= '<img src="https://www.themoviedb.org/t/p/w200/'.$movieinfo['poster_path'].'" alt="" class="floatleft" style="margin-right: 1rem;">';
                $mijnoutput .= '<a href="https://www.imdb.com/title/'.$movieinfo['imdb_id'].'" class="floatright" title="Bekijken op IMDb">IMDb</a>';
                $mijnoutput .= '<p><a href="https://www.themoviedb.org/movie/'.$movieinfo['id'].'">'.$movieinfo['original_title']."</a><br>". $movieinfo['release_date']."</p>";
                $mijnoutput .= '<p><em>'.$movieinfo['tagline']."</em></p>";
                $mijnoutput .= '<p>'.$movieinfo['overview']."</p>";

                $mijnoutput .= "<ul class=\"genres\">";
                foreach ($movieinfo['genres'] as $genre) {
                    $mijnoutput .= '<li>'. $genre['name'] . "</li>";
                }
                $mijnoutput .= "</ul>";

                return $mijnoutput;
            }
        ]
    ]
]);

?>