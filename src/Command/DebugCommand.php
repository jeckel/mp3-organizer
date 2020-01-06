<?php

namespace App\Command;

use App\Service\AudioFileFactory;
use App\Service\ScanManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DebugCommand extends Command
{
    protected static $defaultName = 'app:scan:debug';

    /** @var ScanManager */
    protected $scanManager;
    /**
     * @var AudioFileFactory
     */
    protected $factory;

    /**
     * ScanCommand constructor.
     * @param ScanManager      $scanManager
     * @param AudioFileFactory $factory
     */
    public function __construct(ScanManager $scanManager, AudioFileFactory $factory)
    {
        $this->scanManager = $scanManager;
        parent::__construct();
        $this->factory = $factory;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('file', InputArgument::REQUIRED, 'Argument description')
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
//        $finder = new Finder();

        $filepath = $input->getArgument('file');
        var_dump((new \getID3())->analyze($filepath));

        var_dump($this->factory->fromFile(new \SplFileInfo($filepath)));

//        $path = '/home/jeckel/Corrin/music';
//        $finder->in($path);
//
//        $this->scanManager->scan($finder);

        return 0;
    }
}
