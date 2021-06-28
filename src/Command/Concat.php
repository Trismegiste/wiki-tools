<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class Concat extends Command
{

    protected static $defaultName = 'category:dump';

    protected function configure()
    {
        $this->setDescription('Dumps and agregates a category')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('category', InputArgument::REQUIRED)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'How many', 50);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = 'https://' . $input->getArgument('host') . '/fr/api.php';
        $category = $input->getArgument('category');

        $client = HttpClient::create();
        $response = $client->request('GET', $host, ['query' => [
                'action' => 'query',
                'format' => 'json',
                'list' => 'categorymembers',
                'cmtitle' => 'Catégorie:' . $category,
                'cmlimit' => $input->getOption('limit')
        ]]);

        $result = json_decode($response->getContent());
        $page = $result->query->categorymembers;
        foreach ($page as $item) {
            $output->writeln($item->title);
        }
        
        

        // $response = $client->request('GET', self::$host . '/fr/api.php?action=parse&format=json&pageid=22&prop=text&disablelimitreport=1&disableeditsection=1&disabletoc=1');
        return 0;
    }

}
