<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TorrentCommand extends ContainerAwareCommand
{
    protected $base_url='http://kickass.to/movies/?field=seeders&sorder=desc';
    public function configure()
    {
        $this
            ->setName("torrents:get")
            ->setDescription("retrieves torrents from kickass.to");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $torrentService = $container->get('torrent_service');
        $torrents= $torrentService->getTorrents($this->base_url);
        $output->writeln($torrents);
    }
}