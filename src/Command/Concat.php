<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Concat extends Command
{

    protected static $defaultName = 'wiki:list';

    protected function configure()
    {
        $this->setDescription('List a cat');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new \Symfony\Component\HttpClient\HttpClient();

        return 0;
    }

// /fr/api.php?action=query&format=json&list=categorymembers&continue=-%7C%7C&cmtitle=Cat%C3%A9gorie%3AComp%C3%A9tence&cmcontinue=page%7C4e415649474154494f4e%7C421&cmlimit=30
}
