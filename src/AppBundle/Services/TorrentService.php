<?php
namespace AppBundle\Services;

use Goutte\Client;

class TorrentService
{

    public function getTorrents($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $crawler = $crawler
            ->filter('.torrentname .filmType>a:first-child')
            ->reduce(function ($node, $i) {
                if ($i < 2) {
                    $this->getTorrent($node);

                }
            });
    }

    public function getTorrent($node)
    {
        $client = new Client();
        $linkCrawler = $client->request('GET', $node->link()->getUri());

        $imdbTag = $linkCrawler->filter('ul.block.overauto>li:nth-child(3)>a');

        if (!$imdbTag) {
            return false;
        }
        $imdbTag=$imdbTag->text();
        $this->getImdbInfos($imdbTag);
        $torrentTitle = $linkCrawler->filter('h1');
        if($torrentTitle){
            $torrentTitle = $torrentTitle->text();
        }

        $magnet = $linkCrawler->filter('.buttonsline a[title="Magnet link"]');
        if($magnet){
            $magnet=$magnet->link()->getUri();
            $infoHash = preg_match('/btih:(?<path>\w*)&/', $magnet, $hash);
            $infoHash = $hash['path'];
        }

        $leechers = $linkCrawler->filter('.leechBlock strong[itemprop="leechers"]');
        if($leechers){
            $leechers=$leechers->text();
        }

        $seeders = $linkCrawler->filter('.seedBlock strong[itemprop="seeders"]');
        if($seeders){
            $seeders=$seeders->text();
        }

        $videoQuality = $linkCrawler->filter('ul.block.overauto>li:nth-child(2)>span');
        if ($videoQuality) {
            $videoQuality=$videoQuality->text();
        }
//        $this->setTorrentMovie($torrentTitle,$magnet,$infoHash,$leechers,$seeders,$videoQuality,$imdbTag);

    }

    public function getImdbInfos($tag)
    {
        $client = new Client();
        $linkCrawler = $client->request('GET', 'http://imdb.com/title/tt' . $tag);

        $rating = $linkCrawler->filter('span[itemprop="ratingValue"]');
        if($rating){
            $rating=$rating->text();
        }
        $title = $linkCrawler->filter('h1.header span[itemprop="name"]');
        if($title){
            $title=$title->text();
        }

        $year = $linkCrawler->filter('h1.header span.nobr a');
        if($year){
            $year=$year->text();
        }

        $director = $linkCrawler->filter('div[itemprop="director"] a span');
        if($director){
            $director=$director->text();
        }

        $numberVotes = $linkCrawler->filter('span[itemprop="ratingCount"]');
        if($numberVotes){
            $numberVotes=$numberVotes->text();
        }

        $imgUrl = $linkCrawler->filter('td#img_primary img[itemprop="image"]');
        $imgUrl->each(function ($node) {
            if ($node) {
                $imgUrl=$node->attr('src');
            }
        });

//        $this->setImdbMovie($title,$year,$rating,$director,$numberVotes,$imgUrl);
    }

    public function setImdbMovie($title,$tag,$year,$rating,$director,$numberVotes,$imgUrl){
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        $movieRepo=$doctrine->getRepository("AppBundle:Movie");
        $movie=$movieRepo->findByImdbTag($tag);
        if(!$movie){

            $movie = new Movie();
            if(!empty($title) && !empty($year) && !empty($tag) && !empty($rating) && !empty($director) && !empty($numberVotes) && !empty($imgUrl)){
                $movie->setTitle($title);
                $movie->setImdbTag($tag);
                $movie->setYear($year);
                $movie->setRating($rating);
                $movie->setDirector($director);
                $movie->setnumberVotes($numberVotes);
                $movie->setimgUrl($imgUrl);
            }
            $em=$doctrine->getManager();
            $em->persist($movie);
            $em->flush();
        }
    }

    public function setTorrentMovie($torrentTitle,$magnet,$infoHash,$leechers,$seeders,$videoQuality,$imdbTag){
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        $torrentRepo=$doctrine->getRepository('AppBundle:Torrent');
        $torrent=$torrentRepo->findByInfoHash($infoHash);
        if(!$torrent){
            $torrent=new Torrent();
            if(!empty($torrentTitle) && !empty($magnet) && !empty($infoHash) && !empty($leechers) && !empty($seeders) && !empty($videoQuality) && !empty($imdbTag)){
                $torrent->setTorrentTitle($torrentTitle);
                $torrent->setMagnet($magnet);
                $torrent->setInfoHash($infoHash);
                $torrent->setLeechers($leechers);
                $torrent->setSeeders($seeders);
                $torrent->setVideoQuality($videoQuality);
                $torrent->setImdbTag($imdbTag);
            }
            $em=$doctrine->getManager();
            $em->persist($torrent);
            $em->flush();
        }

    }

}