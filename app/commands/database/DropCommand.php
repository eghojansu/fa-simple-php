<?php

namespace commands\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App;

class DropCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:drop')
            ->setDescription('Drop database')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $db = App::instance()->service('database');
        $db->drop();

        $output->writeln("  <fg=yellow>Database dropped</>");
    }
}