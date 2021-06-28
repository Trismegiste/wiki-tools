<?php

namespace App\Command;

use App\Service\MediaWiki;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;

class CategoryConcat extends Command
{

    protected static $defaultName = 'category:dump';

    protected function configure()
    {
        $this->setDescription('Dumps and agregates a category')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('category', InputArgument::REQUIRED)
            ->addArgument('html', InputArgument::REQUIRED)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'How many', 50);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = new MediaWiki(HttpClient::create(), $input->getArgument('host'));
        $category = $input->getArgument('category');
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('html');
        $filesystem = new Filesystem();

        $io->title("Accessing $category Category...");
        $page = $repository->searchPageFromCategory($category, $input->getOption('limit'));

        $io->success("Found " . \count($page) . ' pages');
        $io->progressStart(\count($page));
        foreach ($page as $item) {
            $title = $item->title;
            $content = $repository->getPage($item->pageid);
            $filesystem->appendToFile($target, "<h1>$title</h1>\n");
            $filesystem->appendToFile($target, $content);
            $io->progressAdvance();
            usleep(100000);
        }
        $io->progressFinish();

        $io->success("$target generated");

        return 0;
    }

}
