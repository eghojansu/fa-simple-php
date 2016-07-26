<?php

namespace commands\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App;

class ClearCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:clear')
            ->setDescription('Clearing database content')
            ->addArgument('table', InputArgument::IS_ARRAY,
                'table to cleared, use [space] to clear multiple table')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // fill table list here
        $tables = [
            'user',
        ];
        $tableToClear = $tables;
        if ($filter = $input->getArgument('table')) {
            foreach (explode(',', $filter) as $table) {
                if ($found = array_search($table, $tables)) {
                    $tableToClear[] = $table;
                }
            }
        } else {
            $tableToClear = $tables;
        }

        $db = App::instance()->service('database');
        $sql = '';
        foreach ($tableToClear as $table) {
            $sql .= "delete from $table;";
        }
        $db->pdo()->exec($sql);
        $count = count($tableToClear);
        $output->writeln("  <fg=yellow>$count table(s) cleared</>");
    }
}