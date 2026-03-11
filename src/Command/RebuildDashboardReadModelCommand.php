<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rebuild-dashboard-read-model',
    description: 'Rebuild dashboard_read_model from dashboard',
)]
class RebuildDashboardReadModelCommand extends Command
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Rebuilding dashboard read model');

        $this->connection->executeStatement('TRUNCATE TABLE dashboard_read_model');
        $count = $this->connection->executeStatement(
            'INSERT INTO dashboard_read_model (id, title, visits, revenue, created_at)
             SELECT id, title, visits, revenue, created_at FROM dashboard'
        );

        $io->success('Read model rebuilt. Rows inserted: ' . $count);

        return Command::SUCCESS;
    }
}
