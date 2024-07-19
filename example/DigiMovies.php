<?php

require_once './vendor/autoload.php';

use Movies\DigiMovies;

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $api = new DigiMovies();
    if (isset($_GET['type']) and !empty($_GET['type'])) {
        switch ($_GET['type']) {

            case 'home':
                echo $api->home();
                break;

            case 'movies':
                echo $api->movies();
                break;

            case 'series':
                echo $api->series();
                break;

            case 'farsiDubbedMovies':
                echo $api->farsiDubbedMovies();
                break;

            case 'animationMovies':
                echo $api->animationMovies();
                break;

            case '3dMovies':
                echo $api->movies3D();
                break;

            case 'mostVisitedSeries':
                echo $api->mostVisitedSeries();
                break;

            case 'koreanSeries':
                echo $api->koreanSeries();
                break;

            case 'farsiDubbedSeries':
                echo $api->farsiDubbedSeries();
                break;

            case 'animationSeries':
                echo $api->animationSeries();
                break;

            case 'animeSeries':
                echo $api->animeSeries();
                break;

            case 'category':
                echo $api->category();
                break;

            case 'search':
                if (isset($_GET['q']) and !empty($_GET['q'])) echo $api->search($_GET['q']);
                else echo json_encode(['error' => "Enter q parameter"], 448);
                break;

            case 'getMovie':
                if (isset($_GET['key']) and !empty($_GET['key'])) echo $api->getMovie($_GET['key']);
                else echo json_encode(['error' => "Enter key parameter"], 448);
                break;

        }
    } else {
        echo $api->home();
    }
}