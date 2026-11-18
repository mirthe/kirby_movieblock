<?php

if (!function_exists('mirthe_movieblock_render')) {
    function mirthe_movieblock_render(string $tmdbid): string
    {
        $result = mirthe_movieblock_fetch($tmdbid);
        $movieData = $result['data'];
        $movieInfo = $movieData['movie'] ?? null;
        $credits = $movieData['credits'] ?? null;

        if (empty($movieInfo) || !is_array($movieInfo) || isset($movieInfo['status_code'])) {
            if (function_exists('errorMonitorLog')) {
                $failureReason = $result['failureReason'] ?? $movieInfo['status_message'] ?? 'TMDB returned no movie data.';
                if (isset($movieInfo['status_code'])) {
                    $failureReason .= ' (TMDB status ' . $movieInfo['status_code'] . ')';
                }
                errorMonitorLog(
                    'MOVIEBLOCK',
                    'TMDB lookup failed for movie ID ' . $tmdbid . ': ' . $failureReason,
                    __FILE__,
                    __LINE__
                );
            }
            return '<div class="well"><div class="well-body">' . t('mirthe.movieblock.not-found') . '</div></div>';
        }

        $poster = isset($movieInfo['poster_path']) ? 'https://www.themoviedb.org/t/p/w200/' . $movieInfo['poster_path'] : '';
        $output = '<div class="well">';
        if ($poster !== '') {
            $output .= '<div class="well-img"><a href="https://www.themoviedb.org/movie/' . $movieInfo['id'] . '"><img src="' . $poster . '" alt="" class="img" width="200" height="300"></a></div>';
        }
        $output .= '<div class="well-body">';
        $output .= '<i class="blockico fa-solid fa-film"></i>';
        $output .= '<p><a href="https://www.themoviedb.org/movie/' . $movieInfo['id'] . '">' . ($movieInfo['original_title'] ?? '') . '</a><br>' . ($movieInfo['release_date'] ?? '') . '</p>';
        $output .= '<p><em>' . ($movieInfo['tagline'] ?? '') . '</em></p>';
        $output .= '<p>' . mb_strimwidth($movieInfo['overview'] ?? '', 0, 300, '&#8230;') . '</p>';

        $output .= '<ul class="cast">';
        if (!empty($credits['cast']) && is_array($credits['cast'])) {
            $i = 0;
            foreach ($credits['cast'] as $castMember) {
                $output .= '<li>' . ($castMember['name'] ?? '') . '</li>';
                if (++$i === 5) break;
            }
        }
        $output .= '</ul>';

        $output .= '<ul class="genres">';
        if (!empty($movieInfo['genres']) && is_array($movieInfo['genres'])) {
            foreach ($movieInfo['genres'] as $genre) {
                $output .= '<li>' . ($genre['name'] ?? '') . '</li>';
            }
        }
        $output .= '</ul>';

        return $output . '</div></div>';
    }
}