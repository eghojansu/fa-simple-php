<?php

namespace app\command\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\core\App;
use app\core\Database;

class ImportCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('db:import')
            ->setDescription('Import schema')
            ->addArgument('schema', InputArgument::IS_ARRAY,
                'schema no to imported, default all schema will be imported')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = App::instance()->debug(true);
        $db = $app->service(Database::class);
        $basePath = $app->get('basePath');
        $schemas = [
            'schema'=>$basePath.'app/schema/1-schema.sql',
            'init'=>$basePath.'app/schema/2-init-data.sql',
            'dummy'=>$basePath.'app/schema/3-dummy-data.sql',
        ];

        if ($filter = $input->getArgument('schema')) {
            $schemaToImport = [];
            foreach ($filter as $key) {
                if (isset($schemas[$key])) {
                    $schemaToImport[] = $schemas[$key];
                }
            }
        } else {
            $schemaToImport = $schemas;
        }

        foreach ($schemaToImport as $schema) {
            $db->import($schema);
            if ($db->pdo()->errorCode() != '00000') {
                break;
            }
            $output->writeln("    - <fg=green>'$schema'</> imported", OutputInterface::VERBOSITY_VERBOSE);
        }

        $error = $db->pdo()->errorInfo();
        if ($error[0] === '00000') {
            $count = count($schemaToImport);
            $output->writeln("  <fg=yellow>$count schema imported</>");
        } else {
            $output->writeln("<error>\n($error[1])$error[2]\n</error>");
        }
    }
}
