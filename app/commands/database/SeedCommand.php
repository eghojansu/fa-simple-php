<?php

namespace commands\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App;

class SeedCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:seed')
            ->setDescription('Seeding database content')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $db = App::instance()->debug()->service('database');
        $recordInserted = 0;
        $tableCounter = 0;

        // ---------------------------------------------------------------------
        //

        // insert user
        $tableCounter++;
        for ($i=0; $i < 100; $i++) {
            $data = [];
            $data['name'] = 'User '.$i;
            $data['username'] = 'user-'.$i;
            $data['password'] = $data['username'];
            $recordInserted += (int) $db->insert('user', $data);
        }

        //
        // ---------------------------------------------------------------------

        $error = trim($db->getError($asString = true, $delimiter = PHP_EOL));
        if ($error) {
            $output->writeln("<error>\n$error\n</error>");
        }

        $output->writeln("  <fg=yellow>$recordInserted record(s) inserted on $tableCounter table(s)</>");
    }
}