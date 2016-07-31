<?php

namespace commands\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App;

class ImportCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:import')
            ->setDescription('Import schema')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = App::instance()->debug(true);
        $db = $app->service('database');
        $basePath = $app->get('basePath');
        $schemas = [
            $basePath.'app/schema/1-schema.sql',
            $basePath.'app/schema/2-user-init.sql',
        ];

        foreach ($schemas as $schema) {
            $db->import($schema);
            if ($db->pdo()->errorCode() != '00000') {
                break;
            }
            $output->writeln("    - <fg=green>'$schema'</> imported", OutputInterface::VERBOSITY_VERBOSE);
        }

        $error = $db->pdo()->errorInfo();
        if ($error[0] === '00000') {
            $count = count($schemas);
            $output->writeln("  <fg=yellow>$count schema imported</>");
        } else {
            $output->writeln("<error>\n($error[1])$error[2]\n</error>");
        }
    }
}