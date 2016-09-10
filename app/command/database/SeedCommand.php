<?php

namespace app\command\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\core\App;
use app\core\BatchInsert;
use app\core\Database;

class SeedCommand extends Command
{
    protected $recordInserted = 0;
    protected $tableCounter = 0;

    public function configure()
    {
        $this
            ->setName('db:seed')
            ->setDescription('Seed database content')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = App::instance()->debug(true);
        $db = $app->service(Database::class);

        // ---------------------------------------------------------------------

        $this
            ->seed('user', function($b) use ($db) {
                $affected = 0;

                $db->exec('alter table user auto_increment = 1');
                for ($i=2; $i < 100; $i++) {
                    $datum = [];
                    $datum['name'] = 'User '.$i;
                    $datum['username'] = 'user-'.$i;
                    $datum['password'] = $datum['username'];
                    $b->add($datum);
                }
                $affected += $b->execute()?$b->count():0;

                return $affected;
            })
        ;

        // ---------------------------------------------------------------------

        $error = trim($db->getError($asString = true, $delimiter = PHP_EOL));
        if ($error) {
            $output->writeln("<error>\n$error\n</error>");
        }
        $output->writeln('<info>'.PHP_EOL.$db->getLog().PHP_EOL.'</info>', OutputInterface::VERBOSITY_DEBUG);

        $output->writeln("  <fg=yellow>{$this->recordInserted} record(s) inserted on {$this->tableCounter} table(s)</>");
    }

    protected function seed($table, $callback)
    {
        $tables = is_array($table)?$table:explode(',', $table);
        $args = [];
        $app= App::instance();
        foreach ($tables as $table) {
            $args[] = $app->service(BatchInsert::class, [$table]);
        }
        $this->tableCounter += count($tables);
        $this->recordInserted += (int) call_user_func_array($callback, $args);

        return $this;
    }
}
