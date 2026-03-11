<?php

namespace App\Command;

use App\Domain\Dashboard\Event\DashboardDataImported;
use App\Domain\Dashboard\Model\Dashboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import-dashboard',
    description: 'Import 100000 dashboard records',
)]
class ImportDashboardDataCommand extends Command
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus)
    {
        parent::__construct();
        $this->em = $em;
        $this->bus = $bus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Importing Dashboard Data');

        for ($i = 1; $i <= 100000; $i++) {

            $dashboard = new Dashboard();
            $dashboard->setTitle('Site '.$i);
            $dashboard->setVisits(rand(100, 10000));
            $dashboard->setRevenue(rand(10, 500));
            $dashboard->setCreatedAt(new \DateTime());

            $this->em->persist($dashboard);

            // Flush every 500 rows (performance optimization)
            if ($i % 500 === 0) {
                $this->em->flush();
                $this->em->clear();
                $io->text("Inserted: $i");
            }
        }

        $this->em->flush();

        $io->success('100000 records inserted successfully.');

        // Dispatch domain event to rebuild read model asynchronously
        $this->bus->dispatch(new DashboardDataImported(100000));

        return Command::SUCCESS;
    }
}
