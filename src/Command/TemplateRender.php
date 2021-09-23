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

class TemplateRender extends Command
{

    protected static $defaultName = 'template:render';

    protected function configure()
    {
        $this->setDescription('Render a wiki template')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('template', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = new MediaWiki(HttpClient::create(), $input->getArgument('host'));
        $io = new SymfonyStyle($input, $output);
        $template = $input->getArgument('template');
        $filesystem = new Filesystem();

        $loader = new FilesystemLoader(__DIR__ . '/../../template');
        $twig = new Environment($loader);
        $twig->addExtension(new MediaWikiExtension($repository));

        //    $filesystem->dumpFile($template . '.html', $twig->render($template . '.html.twig', ['titre' => $template]));

        $params = $repository->getTemplateData($template);

        $io->title($template);
        foreach ($params as $key) {
            $io->ask("Value for $key");
        }

        return 0;
    }

}
