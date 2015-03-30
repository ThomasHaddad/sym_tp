<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Torrent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TorrentRepository")
 */
class Torrent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="torrentTitle", type="string", length=255)
     */
    private $torrentTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="magnet", type="string", length=255)
     */
    private $magnet;

    /**
     * @var string
     *
     * @ORM\Column(name="infoHash", type="string", length=255)
     */
    private $infoHash;

    /**
     * @var string
     *
     * @ORM\Column(name="leechers", type="string", length=255)
     */
    private $leechers;

    /**
     * @var string
     *
     * @ORM\Column(name="seeders", type="string", length=255)
     */
    private $seeders;

    /**
     * @var string
     *
     * @ORM\Column(name="videoQuality", type="string", length=255)
     */
    private $videoQuality;

    /**
     * @var string
     *
     * @ORM\Column(name="imdbTag", type="string", length=255)
     */
    private $imdbTag;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set torrentTitle
     *
     * @param string $torrentTitle
     * @return Torrent
     */
    public function setTorrentTitle($torrentTitle)
    {
        $this->torrentTitle = $torrentTitle;

        return $this;
    }

    /**
     * Get torrentTitle
     *
     * @return string 
     */
    public function getTorrentTitle()
    {
        return $this->torrentTitle;
    }

    /**
     * Set magnet
     *
     * @param string $magnet
     * @return Torrent
     */
    public function setMagnet($magnet)
    {
        $this->magnet = $magnet;

        return $this;
    }

    /**
     * Get magnet
     *
     * @return string 
     */
    public function getMagnet()
    {
        return $this->magnet;
    }

    /**
     * Set infoHash
     *
     * @param string $infoHash
     * @return Torrent
     */
    public function setInfoHash($infoHash)
    {
        $this->infoHash = $infoHash;

        return $this;
    }

    /**
     * Get infoHash
     *
     * @return string 
     */
    public function getInfoHash()
    {
        return $this->infoHash;
    }

    /**
     * Set leechers
     *
     * @param string $leechers
     * @return Torrent
     */
    public function setLeechers($leechers)
    {
        $this->leechers = $leechers;

        return $this;
    }

    /**
     * Get leechers
     *
     * @return string 
     */
    public function getLeechers()
    {
        return $this->leechers;
    }

    /**
     * Set seeders
     *
     * @param string $seeders
     * @return Torrent
     */
    public function setSeeders($seeders)
    {
        $this->seeders = $seeders;

        return $this;
    }

    /**
     * Get seeders
     *
     * @return string 
     */
    public function getSeeders()
    {
        return $this->seeders;
    }

    /**
     * Set videoQuality
     *
     * @param string $videoQuality
     * @return Torrent
     */
    public function setVideoQuality($videoQuality)
    {
        $this->videoQuality = $videoQuality;

        return $this;
    }

    /**
     * Get videoQuality
     *
     * @return string 
     */
    public function getVideoQuality()
    {
        return $this->videoQuality;
    }

    /**
     * Set imdbTag
     *
     * @param string $imdbTag
     * @return Torrent
     */
    public function setImdbTag($imdbTag)
    {
        $this->imdbTag = $imdbTag;

        return $this;
    }

    /**
     * Get imdbTag
     *
     * @return string 
     */
    public function getImdbTag()
    {
        return $this->imdbTag;
    }
}
