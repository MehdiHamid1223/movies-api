# Movies Api
##This is a free movie and TV series web service library connected to the DigiMovies website for front-end developers.

## Requirements
Movies Api depends on PHP 8.0+.

## **Installation**
Add ```mehdihamid/movies-api``` as a require dependency in your ```composer.json``` file:

    composer require mehdihamid/movies-api

## **Usage**
Create a Goutte Client instance 

```php
use Goutte\Client;

$client = new Client();
```

Get a list of categories
```php
echo $api->category();
```

Search Movie or Series
```php
echo $api->search("the boy");
```

Receive Movies
```php
echo $api->home(); // Get Home Page Movies
echo $api->movies(); // Get Movies
echo $api->farsiDubbedMovies(); // Get Farsi Dubbed Movies
echo $api->animationMovies(); // Get Animation Movies
echo $api->movies3D(); // Get 3D Movies
```

Receive Series
```php
echo $api->series(); // Get Series
echo $api->mostVisitedSeries(); // Get Most Visited Series
echo $api->koreanSeries(); // Get Korean Series
echo $api->farsiDubbedSeries(); // Get Farsi Dubbed Series
echo $api->animationSeries(); // Get Animation Series
echo $api->animeSeries(); // Get Anime Series
```

Get Download Link Movie or Series
```php
$api->getMovie("serie/the-boys-7"); // The received key from the list of videos or series
```

<br/>

If this project is helpful to you, you may wish to give it a🌟
- TRX : ```TZ7zUPVnVTRnatn1JZtRQVAzfFfJ9xmjjQ```
