<?php namespace Movies;

use Goutte\Client;

class DigiMovies {

    private array $routes = [
        'base' => "https://digimoviez.com",
        'movies' => "https://digimoviez.com/movies",
        'series' => "https://digimoviez.com/series",
        'farsiDubbedMovies' => "https://digimoviez.com/category/farsi-dubbed",
        'animationMovies' => "https://digimoviez.comgenre/animation",
        '3dMovies' => "https://digimoviez.com/category/3d",
        'mostVisitedSeries' => "https://digimoviez.com/category/%d9%be%d8%b1%d8%a8%d8%a7%d8%b2%d8%af%db%8c%d8%af%d8%aa%d8%b1%db%8c%d9%86-%d8%b3%d8%b1%db%8c%d8%a7%d9%84-%d9%87%d8%a7",
        'koreanSeries' => "https://digimoviez.com/category/%d8%b3%d8%b1%db%8c%d8%a7%d9%84-%da%a9%d8%b1%d9%87-%d8%a7%db%8c",
        'farsiDubbedSeries' => "https://digimoviez.com/category/%d8%b3%d8%b1%db%8c%d8%a7%d9%84-%d8%af%d9%88%d8%a8%d9%84%d9%87-%d9%81%d8%a7%d8%b1%d8%b3%db%8c",
        'animationSeries' => "https://digimoviez.com/category/%d8%b3%d8%b1%db%8c%d8%a7%d9%84-%d8%a7%d9%86%db%8c%d9%85%db%8c%d8%b4%d9%86%db%8c",
        'animeSeries' => "https://digimoviez.com/category/%d8%a7%d9%86%db%8c%d9%85%d9%87"
    ];

    private string $my_server;

    public function __construct()
    {
        $this->my_server = 'https://' . $_SERVER['HTTP_HOST'];
    }

    private function getMovies($url, $page = false): string
    {

        try {
            if ($page) {
                $url .= "/page/" . $page;
            }

            $client = new Client();
            $crawler = $client->request('GET', $url);

            $movies = [];

            $crawler->filter('.body_favorites .item_small_loop')->each(function ($node) use (&$movies) {

                $movie = [];
                $movie['id'] = $node->attr('data-id');

                $node->filter('a')->each(function ($node) use (&$movie) {
                    $movie['title'] = str_replace("دانلود ", null, $node->attr('title'));
                    $key = str_replace($this->routes['base'] . "/", null , $node->attr('href'));
                    $key = str_replace("/", null , $key);
                    $url = $this->my_server . "?type=getMovie&key=" . $key;
                    $movie['url'] = $url;

                    $node->filter('.cover img')->each(function ($node) use (&$movie) {
                        $movie['cover'] = $node->attr('src');
                    });

                });

                $movies['data'][] = $movie;

            });

            $pageNumbers = $crawler->filter('.alphapageNavi div.inner_alphapageNavi');
            $pageNumbersCount = $pageNumbers->children()->count();
            $lastPageNumber = $pageNumbers->children()->eq($pageNumbersCount - 2)->text();

            $movies['lastPage'] = $lastPageNumber;

            return json_encode([
                'success' => true,
                'error' => null,
                'result' => $movies
            ], 448);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], 448);
        }

    }

    private function getMovies2($url, $page = false): string {

        try {
            if ($page) {
                $url .= "/page/" . $page;
            }

            $client = new Client();
            $crawler = $client->request('GET', $url);

            $movies = [];

            $crawler->filter('.main_site .item_def_loop')->each(function ($node) use (&$movies) {

                $movie = [];
                $data_list = [
                    "کیفیت :" => "quality",
                    "زمان :" => "time",
                    "ژانر :" => "genre",
                    "کارگردان :" => "director",
                    "ستارگان :" => "actor",
                    "محصول کشور :" => "country",
                    "نویسنده :" => "writer",
                    "رده سنی :" => "Ages"
                ];

                $node->filter('.title_h a')->each(function ($node) use (&$movie) {
                    $key = str_replace($this->routes['base'] . "/", null , $node->attr('href'));
                    $key = str_replace("/", null , $key);
                    $url = $this->my_server . "?type=getMovie&key=" . $key;
                    $movie['url'] = $url;
                    $movie['title'] = str_replace("دانلود ", null, $node->attr('title'));
                });

                $node->filter('.cover img')->each(function ($node) use (&$movie) {
                    $movie['cover'] = $node->attr('src');
                });

                $node->filter('.show_trailer')->each(function ($node) use (&$movie) {
                    $movie['trailer'] = $node->attr('data-trailerlink');
                });

                $node->filter('.meta_loop .meta_item > ul li')->each(function ($node) use (&$movie, &$data_list) {

                    $data = '';
                    $value = '';

                    $node->filter('.lab_item')->each(function ($node) use (&$data) {
                        if (!empty($node->text())) $data = $node->text();
                    });

                    $node->filter('.res_item')->each(function ($node) use (&$value) {
                        if (!empty($node->text())) $value = $node->text();
                    });

                    if (!empty($data) and !empty($value)) {
                        if ($data_list[$data] != 'time' and $data_list[$data] != 'director' and $data_list[$data] != 'quality') {
                            $movie[$data_list[$data]] = explode(",", $value);
                        } else {
                            $movie[$data_list[$data]] = $value;
                        }
                    }

                });

                $node->filter('.plot_text')->each(function ($node) use (&$movie) {
                    $movie['short_description'] = $node->text();
                });

                $node->filter('.orangelab')->each(function ($node) use (&$movie) {
                    $movie['score'] = $node->text();
                });

                $movies['data'][] = $movie;

            });

            $pageNumbers = $crawler->filter('.main_site .alphapageNavi div.inner_alphapageNavi');
            if ($pageNumbers->children()->count() > 0) {
                $pageNumbersCount = $pageNumbers->children()->count();
                $lastPageNumber = $pageNumbers->children()->eq($pageNumbersCount - 2)->text();

                $movies['lastPage'] = $lastPageNumber;
            }

            return json_encode([
                'success' => true,
                'error' => null,
                'result' => $movies
            ], 448);

        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], 448);
        }

    }

    public function getMovie($key): string {

        try {

            $client = new Client();
            $crawler = $client->request('GET', $this->routes['base']. '/' . $key);

            $movie_data = [];

            $crawler->filter('#main_page .post_holder')->each(function ($node) use (&$movie_data, &$key) {

                $data_list = [
                    "کیفیت :" => "quality",
                    "زمان :" => "time",
                    "ژانر :" => "genre",
                    "کارگردان :" => "director",
                    "ستارگان :" => "actor",
                    "محصول کشور :" => "country",
                    "نویسنده :" => "writer",
                    "رده سنی :" => "Ages",
                    "شبکه :" => "Channel",
                ];

                $node->filter('.on_inner_post_holder > .cover > .inner_cover > a')->each(function ($node) use (&$movie_data) {
                    $key = str_replace($this->routes['base'] . "/", null , $node->attr('href'));
                    $key = str_replace("/", null , $key);
                    $url = $this->my_server . "?type=getMovie&key=" . $key;
                    $movie_data['url'] = $url;
                    $movie_data['title'] = str_replace("دانلود ", null, $node->attr('title'));


                    $node->filter('img')->each(function ($node) use (&$movie_data) {
                        $movie_data['cover'] = $node->attr('src');
                    });

                });

                $node->filter('.on_trailer_bottom')->each(function ($node) use (&$movie_data) {
                    $movie_data['trailer'] = $node->attr('data-trailerlink');
                });

                $node->filter('.inner_post_holder')->each(function ($node) use (&$movie_data) {
                    $background = $node->attr('style');
                    $background = str_replace("background-image: url('", null, $background);
                    $background = str_replace("')", null, $background);
                    $movie_data['background'] = $background;
                });

                $node->filter('.meta > .single_meta_data.meta_item > ul li')->each(function ($node) use (&$movie_data, &$data_list) {

                    $data = '';
                    $value = '';

                    $node->filter('.lab_item')->each(function ($node) use (&$data) {
                        if (!empty($node->text())) $data = $node->text();
                    });

                    $node->filter('.res_item')->each(function ($node) use (&$value) {
                        if (!empty($node->text())) $value = $node->text();
                    });

                    if (!empty($data) and !empty($value)) {
                        if ($data_list[$data] != 'time' and $data_list[$data] != 'director' and $data_list[$data] != 'quality') {
                            $movie_data[$data_list[$data]] = explode(",", $value);
                        } else {
                            $movie_data[$data_list[$data]] = $value;
                        }
                    }

                });

                $node->filter('.redlab')->each(function ($node) use (&$movie_data) {
                    $movie_data['score'] = $node->text();
                });

                $node->filter('.plot_text')->each(function ($node) use (&$movie_data) {
                    $movie_data['short_description'] = $node->text();
                });

                $node->filter('.inner_content_box_info')->each(function ($node) use (&$movie_data) {
                    $movie_data['description'] = $node->text();
                });

                if (strpos($key, "serie/") !== false) {
                    $node->filter('.dllinks > .dllink_holder_ham > .body_dllink')->each(function ($node) use (&$movie_data) {

                        $node->filter('.item_row_series.parent_item')->each(function ($node) use (&$movie_data) {

                            $download = [];

                            $node->filter('.head_season')->each(function ($node) use (&$download) {

                                $node->filter('.side_right .title_row')->each(function ($node) use (&$download) {
                                    $node->filter('b')->each(function ($node) use (&$download) {
                                        $download['parts'] = $node->text();
                                    });

                                    $node->filter('h3')->each(function ($node) use (&$download) {
                                        $download['season'] = str_replace("فصل : ", null, $node->text());
                                    });
                                });

                                $node->filter('.side_left')->each(function ($node) use (&$download) {

                                    $node->filter('.encoder_dl')->each(function ($node) use (&$download) {
                                        $download['encoder'] = str_replace('Encoder : ', null, $node->text());
                                    });

                                    $node->filter('.size_dl')->each(function ($node) use (&$download) {
                                        $download['size'] = $node->text();
                                    });

                                    $node->filter('.format_dl')->each(function ($node) use (&$download) {
                                        $download['format'] = $node->text();
                                    });

                                });

                            });

                            $node->filter('.parts > .inner_parts_holder a.partlink')->each(function ($node) use (&$download) {
                                $download['urls'][] = [
                                    'part' => str_replace("دانلود قسمت ", null, $node->attr("title")),
                                    'url' => $node->attr('href')
                                ];
                            });

                            $movie_data['download_links'][] = $download;

                        });

                    });
                } else {
                    $node->filter('.boxdl_movies > .boxdl_body > .dllink_holder_ham')->each(function ($node) use (&$movie_data) {
                        $download = [];

                        $node->filter('.title_dllink > .right_title')->each(function ($node) use (&$download) {
                            $download['name'] = $node->text();
                        });

                        $node->filter('.body_dllink_movies > .itemdl.parent_item > .row_data')->each(function ($node) use (&$download) {

                            $url = [];

                            $node->filter('.side_right')->each(function ($node) use (&$url) {

                                $node->filter('.btn_row.btn_dl')->each(function ($node) use (&$url) {
                                    $url['url'] = $node->attr("href");
                                });

                            });

                            $node->filter('.side_left')->each(function ($node) use (&$url) {

                                $node->filter('.head_left_side h3')->each(function ($node) use (&$url) {
                                    $url['name'] = $node->text();
                                });

                                $node->filter('.meta')->each(function ($node) use (&$url) {

                                    $node->filter('.encoder_dl')->each(function ($node) use (&$url) {
                                        $url['encoder'] = str_replace('Encoder : ', null, $node->text());
                                    });

                                    $node->filter('.size_dl')->each(function ($node) use (&$url) {
                                        $url['size'] = $node->text();
                                    });

                                    $node->filter('.format_dl')->each(function ($node) use (&$url) {
                                        $url['format'] = $node->text();
                                    });

                                });

                            });

                            $download['urls'][] = $url;

                        });

                        $movie_data['download_links'][] = $download;

                    });
                }

            });

            return json_encode([
                'success' => true,
                'error' => null,
                'result' => $movie_data
            ], 448);

        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], 448);
        }

    }

    public function category(): string {

        $client = new Client();
        $crawler = $client->request('GET', $this->routes['base']);

        $categorys = [];

        $crawler->filter('.box_sidebar.category_list ul li')->each(function ($node) use (&$categorys) {

            $is_series = false;
            $category = [];

            $node->filter('a')->each(function ($node) use (&$category, &$is_series) {
                $url = $node->attr('href');
                if (strpos($url, "/series-genre/") !== false) {
                    $is_series = true;
                } else {
                    $is_series = false;
                }
                $category['url'] = $url;
                $category['name'] = $node->attr('title');
            });

            $node->filter('span')->each(function ($node) use (&$category) {
                $category['count'] = str_replace('(', null, str_replace(')', null, $node->text()));
            });

            $is_series ? $categorys['series'][] = $category : $categorys['movies'][] = $category;

        });

        return json_encode($categorys, 448);
    }

    public function home($page = false): string {
        return $this->getMovies2($this->routes['base'] ,$page);
    }

    public function movies($page = false): string {
        return $this->getMovies($this->routes['movies'] ,$page);
    }

    public function farsiDubbedMovies($page = false): string {
        return $this->getMovies2($this->routes['farsiDubbedMovies'] ,$page);
    }

    public function animationMovies($page = false): string {
        return $this->getMovies2($this->routes['animationMovies'] ,$page);
    }

    public function movies3D($page = false): string {
        return $this->getMovies2($this->routes['3dMovies'] ,$page);
    }

    public function series($page = false): string {
        return $this->getMovies($this->routes['series'] ,$page);
    }

    public function mostVisitedSeries($page = false): string {
        return $this->getMovies2($this->routes['mostVisitedSeries'] ,$page);
    }

    public function koreanSeries($page = false): string {
        return $this->getMovies2($this->routes['koreanSeries'] ,$page);
    }

    public function farsiDubbedSeries($page = false): string {
        return $this->getMovies2($this->routes['farsiDubbedSeries'] ,$page);
    }

    public function animationSeries($page = false): string {
        return $this->getMovies2($this->routes['animationSeries'] ,$page);
    }

    public function animeSeries($page = false): string {
        return $this->getMovies2($this->routes['animeSeries'] ,$page);
    }

    public function search($q, $page = false): string {
        return $this->getMovies2($this->routes['base'] . "?s=" . $q ,$page);
    }

}
