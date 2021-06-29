<?php

namespace App\Command;

use App\Service\MediaWiki;
use App\Twig\MediaWikiExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DocumentRender extends Command
{

    protected static $defaultName = 'document:render';

    protected function configure()
    {
        $this->setDescription('Render a twig')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('html', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = new MediaWiki(HttpClient::create(), $input->getArgument('host'));
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('html');
        $filesystem = new Filesystem();

        $loader = new FilesystemLoader(__DIR__ . '/../template');
        $twig = new Environment($loader);
        $twig->addExtension(new MediaWikiExtension($repository));

        $filesystem->dumpFile($target, $twig->render('index.html.twig'));

        return 0;
    }

}
