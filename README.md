# Kirby Plugin: Movieblock

This plugin allows you to show information for a movie from the TheMovieDB API. 
Though that might change to a different service at some later point

## Git submodule

```
git submodule add https://github.com/mirthe/kirby_movieblock site/plugins/movieblock
```

## Usage

You'll need an API key for this from https://developers.themoviedb.org/3/getting-started/authentication
Add the following to your config where XX is your key:

    'themoviedb.apiKey' => 'XX'

## Example 

Placed for example with 

    (movie: tmdb: 577922)

![Example of plugin in use](image: /example.png?raw=true)

## Todo

- Offer as an official Kirby plugin
- Might use other service(s)
- Add sample SCSS to this readme
- Cleanup code
- Lots..