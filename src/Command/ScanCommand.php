<?php

namespace App\Command;

use App\Service\ScanManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ScanCommand extends Command
{
    protected static $defaultName = 'app:scan';

    /** @var ScanManager */
    protected $scanManager;

    /**
     * ScanCommand constructor.
     * @param ScanManager $scanManager
     */
    public function __construct(ScanManager $scanManager)
    {
        $this->scanManager = $scanManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();

        $path = '/home/jeckel/Corrin/music';
        $finder->in($path);

        $this->scanManager->scan($finder);

        return 0;
    }
}
