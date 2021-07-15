<?php

namespace App\Command;

use App\Service\MediaWiki;
use App\Twig\MediaWikiExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CategoryCompil extends Command
{

    protected static $defaultName = 'category:compil';

    protected function configure()
    {
        $this->setDescription('Extracts all pages from a category and makes stats')
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
        $listing = [];
        foreach ($page as $item) {
            $title = $item->title;
            $content = $repository->getWikitextByName($title);
            $regex = '#\\{\\{InfoboxMorphe\\|([^}]+)\\}\\}#';
            if (preg_match($regex, $content, $matches)) {
                $templateParam = ['title' => $title];
                $pairParam = explode('|', $matches[1]);
                foreach ($pairParam as $pair) {
                    list($key, $value) = explode('=', $pair);
                    $templateParam[$key] = $value;
                }
                $listing[] = $templateParam;
            }
            $io->progressAdvance();
            usleep(100000);
        }
        $io->progressFinish();

        $loader = new FilesystemLoader(__DIR__ . '/../../template');
        $twig = new Environment($loader);
        $twig->addExtension(new MediaWikiExtension($repository));

        $filesystem->dumpFile($target, $twig->render('morphe.html.twig', ['listing' => $listing]));

        $io->success("$target generated");

        return 0;
    }

}
