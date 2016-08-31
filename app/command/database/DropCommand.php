<?php

namespace app\command\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\core\App;
use app\core\Database;

class DropCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('db:drop')
            ->setDescription('Drop database')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $db = App::instance()->service(Database::class);
        $db->drop();

        $output->writeln("  <fg=yellow>Database dropped</>");
    }
}
