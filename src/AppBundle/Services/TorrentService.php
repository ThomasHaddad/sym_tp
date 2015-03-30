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

        $movieTitle = $linkCrawler->filter('h1');
        if($movieTitle){
            $movieTitle = $movieTitle->text();
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
        $imdbTag = $linkCrawler->filter('ul.block.overauto>li:nth-child(3)>a');

        if ($imdbTag) {
            $imdbTag=$imdbTag->text();
            $this->getImdbInfos($imdbTag);
//            $this->setTorrent($movieTitle,$infoHash,$leechers,$seeders,$videoQuality);
        }

    }

    public function getImdbInfos($tag)
    {
        $client = new Client();
        $linkCrawler = $client->request('GET', 'http://imdb.com/title/tt' . $tag);

        $rating = $linkCrawler->filter('span[itemprop="ratingValue"]');
        if($rating){
            $rating=$rating->text();
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

    }

    public function setTorrent(){

    }

    public function setImdb(){

    }
}