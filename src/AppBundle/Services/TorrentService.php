<?php
namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Torrent;
use Goutte\Client;

class TorrentService
{
    protected $doctrine;
    protected $validator;
    protected $arrayMovie = [];
    protected $arrayTorrent = [];
    protected $arrayCategories = [];

    function __construct($doctrine, $validator)
    {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
    }

    public function getTorrents($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        if (!$crawler) {
            return "Issues while retrieving datas";
        }
        $crawler = $crawler
            ->filter('.torrentname .filmType>a:first-child')
            ->reduce(function ($node, $i) {
                if ($i < 40) {
                    $this->getTorrent($node);
                    if ($this->getTorrent($node)) {
                        $movie = $this->setImdbMovie($this->arrayMovie, $this->arrayCategories);
                        if ($movie) {
                            $this->setTorrentMovie($this->arrayTorrent, $movie);
                        }
                    } else {
                        return "Movie not in IMDB";
                    }
                }
            });
        return "Datas retrieved";
    }

    public function getTorrent($node)
    {
        $client = new Client();
        $linkCrawler = $client->request('GET', $node->link()->getUri());
        $imdbTag = '';
        $linkCrawler->filter('ul.block.overauto>li:nth-child(3)>a')->each(function ($node) use (&$imdbTag) {
            if ($node) {
                $imdbTag = $node->text();
                $this->getImdbInfos($imdbTag);
            }
        });

        $torrentTitle = "";
        $linkCrawler->filter('h1')->each(function ($node) use (&$torrentTitle) {
            if ($node) {
                $torrentTitle = $node->text();
            }
        });

        $magnet = "";
        $infoHash = "";
        $linkCrawler->filter('.buttonsline a[title="Magnet link"]')->each(function ($node) use (&$magnet, &$infoHash) {
            if ($node) {
                $magnet = $node->link()->getUri();
                $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);
                $infoHash = $hash['path'];
            }

        });

        $leechers = "";
        $linkCrawler->filter('.leechBlock strong[itemprop="leechers"]')->each(function ($node) use (&$leechers) {
            if ($node) {
                $leechers = $node->text();
            }
        });
        $seeders = "";
        $linkCrawler->filter('.seedBlock strong[itemprop="seeders"]')->each(function ($node) use (&$seeders) {
            if ($node) {
                $seeders = $node->text();
            }

        });

        $videoQuality = "";
        $linkCrawler->filter('ul.block.overauto>li:nth-child(2)>span')->each(function ($node) use (&$videoQuality) {
            if ($node) {
                $videoQuality = $node->text();
            }

        });
        $this->arrayTorrent = [];
        array_push($this->arrayTorrent, $torrentTitle, $magnet, $infoHash, $leechers, $seeders, $videoQuality, $imdbTag);
        return $this->arrayTorrent;

    }

    public function getImdbInfos($tag)
    {
        $client = new Client();
        $linkCrawler = $client->request('GET', 'http://imdb.com/title/tt' . $tag);

        $rating = 0;
        $linkCrawler->filter('span[itemprop="ratingValue"]')->each(function ($node) use (&$rating) {
            if ($node) {
                $rating = floatval($node->text());
            }
        });

        $title = "";
        $linkCrawler->filter('h1.header span[itemprop="name"]')->each(function ($node) use (&$title) {

            if ($node) {
                $title = $node->text();
            }
        });

        $year = "";
        $linkCrawler->filter('h1.header span.nobr a')->each(function ($node) use (&$year) {

            if ($node) {
                $year = $node->text();
            }
        });

        $director = "";
            $linkCrawler->filter('div[itemprop="director"] a span')->each(function ($node) use (&$director) {

            if ($node) {
                $director = $node->text();
            }
        });

        $numberVotes = "";
            $linkCrawler->filter('span[itemprop="ratingCount"]')->each(function ($node) use (&$numberVotes) {
            if ($node) {
                $numberVotes = $node->text();
            }
        });
        $imgUrl = "";
        $linkCrawler->filter('td#img_primary img[itemprop="image"]')->each(function ($node) use (&$imgUrl) {
            if ($node) {
                $imgUrl = $node->attr('src');
            }
        });
        $this->arrayMovie = [];
        $this->arrayCategories = [];
        array_push($this->arrayMovie, $title, $tag, $year, $rating, $director, $numberVotes, $imgUrl);


        $linkCrawler
            ->filter('.infobar span[itemprop="genre"]')
            ->each(function ($node) {
                array_push($this->arrayCategories, $node->text());
            });
        return $this->arrayMovie;
    }

    public function setImdbMovie($array, $arrayCat)
    {


        $movieRepo = $this->doctrine->getRepository("AppBundle:Movie");
        $movie = $movieRepo->findOneByImdbTag($array[1]);
        if (!$movie) {

            $movie = new Movie();
            if (!empty($array[0]) && !empty($array[1]) && !empty($array[2]) && !empty($array[3]) && !empty($array[4]) && !empty($array[5]) && !empty($array[6])) {
                $movie->setTitle($array[0]);
                $movie->setImdbTag($array[1]);
                $movie->setYear($array[2]);
                $movie->setRating($array[3]);
                $movie->setDirector($array[4]);
                $movie->setnumberVotes($array[5]);
                $movie->setimgUrl($array[6]);

                $categoryRepo = $this->doctrine->getRepository("AppBundle:Category");
                foreach ($arrayCat as $name) {
                    $category = $categoryRepo->findOneByName($name);
                    if (!$category) {
                        $category = new Category();
                        $category->setName($name);

                        $em = $this->doctrine->getManager();
                        $em->persist($category);
                        $em->flush();

                    }
                    $movie->addCategory($category);
                }
                $errList = $this->validator->validate($movie);
                if (count($errList) < 1) {
                    $em = $this->doctrine->getManager();
                    $em->persist($movie);
                    $em->flush();
                    return $movie;
                }
            }
        } else {
            if (!empty($array[3]) && !empty($array[5])) {

                $movie->setnumberVotes($array[5]);
                $movie->setRating($array[3]);
                $em = $this->doctrine->getManager();
                $em->persist($movie);
                return $movie;
            }
        }
    }

    public function setTorrentMovie($array, $movie)
    {

        $torrentRepo = $this->doctrine->getRepository('AppBundle:Torrent');
        $torrent = $torrentRepo->findOneByInfoHash($array[2]);
        if (!$torrent) {
            $torrent = new Torrent();
            if (!empty($array[0]) && !empty($array[1]) && !empty($array[2]) && !empty($array[3]) && !empty($array[4]) && !empty($array[5]) && !empty($array[6])) {
                $torrent->setTorrentTitle($array[0]);
                $torrent->setMagnet($array[1]);
                $torrent->setInfoHash($array[2]);
                $torrent->setLeechers($array[3]);
                $torrent->setSeeders($array[4]);
                $torrent->setVideoQuality($array[5]);
                $torrent->setImdbTag($array[6]);
                $torrent->setMovie($movie);

                $em = $this->doctrine->getManager();
                $em->persist($torrent);
                $em->flush();
            }
        }

    }

}