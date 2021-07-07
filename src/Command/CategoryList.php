<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Command;

use App\Service\MediaWiki;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Description of CategoryList
 *
 * @author flo
 */
class CategoryList extends Command
{

    protected static $defaultName = 'category:list';

    protected function configure()
    {
        $this->setDescription('Dumps and agregates a category')
                ->addArgument('host', InputArgument::REQUIRED)
                ->addArgument('category', InputArgument::REQUIRED)
                ->addArgument('target', InputArgument::REQUIRED)
                ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'How many', 50);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = new MediaWiki(HttpClient::create(), $input->getArgument('host'));
        $category = $input->getArgument('category');
        $page = $repository->searchPageFromCategory($category, $input->getOption('limit'));
        foreach ($page as $item) {
            $title = $item->title;
            $output->writeln($title);
        }

        return 0;
    }

}
