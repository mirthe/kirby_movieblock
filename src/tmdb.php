<?php

if (!function_exists('mirthe_movieblock_request')) {
    function mirthe_movieblock_request(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $data = json_decode($rawResponse, true);
        $failure = null;

        if ($rawResponse === false) {
            $failure = 'TMDB request failed: ' . $curlError;
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            $failure = 'TMDB request returned HTTP ' . $httpCode;
        }

        if (!empty($data['status_message'])) {
            $failure = $data['status_message'];
            if ($httpCode < 200 || $httpCode >= 300) {
                $failure .= ' (HTTP ' . $httpCode . ')';
            }
        }

        return [
            'data' => $data,
            'failure' => $failure
        ];
    }
}

if (!function_exists('mirthe_movieblock_fetch')) {
    function mirthe_movieblock_fetch(string $tmdbid): array
    {
        $cache = kirby()->cache('mirthe.movieblock');
        $cacheKey = 'tmdb-' . $tmdbid;
        $cachedData = $cache->get($cacheKey);

        if ($cachedData !== null) {
            return [
                'data' => $cachedData,
                'failureReason' => null
            ];
        }

        $apiKey = option('themoviedb.apiKey');
        $baseUrl = 'https://api.themoviedb.org/3/movie/' . $tmdbid;
        $movieResponse = mirthe_movieblock_request($baseUrl . '?api_key=' . $apiKey);
        $creditsResponse = mirthe_movieblock_request($baseUrl . '/credits?api_key=' . $apiKey);

        $movieData = [
            'movie' => $movieResponse['data'],
            'credits' => $creditsResponse['data']
        ];
        $cache->set($cacheKey, $movieData, 604800);

        return [
            'data' => $movieData,
            'failureReason' => $movieResponse['failure']
        ];
    }
}