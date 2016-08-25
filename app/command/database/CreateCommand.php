<?php

namespace app\command\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\App;
use app\Database;

class CreateCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('db:create')
            ->setDescription('Create database')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $db = App::instance()->service(Database::class);
        $db->create();

        $error = $db->pdo()->errorInfo();
        if ($error[0] === '00000') {
            $output->writeln("  <fg=yellow>Database created</>");
        } else {
            $output->writeln("<error>\n($error[1])$error[2]\n</error>");
        }
    }
}
