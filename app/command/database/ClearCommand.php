<?php

namespace app\command\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\core\App;
use app\core\Database;

class ClearCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('db:clear')
            ->setDescription('Clear database content')
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
        if ($filter = $input->getArgument('table')) {
            $tableToClear = [];
            foreach (explode(',', $filter) as $table) {
                if ($found = array_search($table, $tables)) {
                    $tableToClear[] = $table;
                }
            }
        } else {
            $tableToClear = $tables;
        }

        $db = App::instance()->service(Database::class);
        $sql = '';
        foreach ($tableToClear as $table) {
            $sql .= "delete from $table;";
        }
        $db->exec($sql);
        $count = count($tableToClear);
        $output->writeln("  <fg=yellow>$count table(s) cleared</>");
    }
}
