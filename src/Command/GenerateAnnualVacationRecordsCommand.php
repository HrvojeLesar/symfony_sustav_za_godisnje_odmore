<?php

namespace App\Command;

use App\Repository\AnnualVacationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'GenerateAnnualVacationRecords',
    description: 'Command for a CRON job used to generate new annaul vacation records the database.',
)]
class GenerateAnnualVacationRecordsCommand extends Command
{
    private AnnualVacationRepository $annualVacationRepository;

    public function __construct(AnnualVacationRepository $annualVacationRepository)
    {
        $this->annualVacationRepository = $annualVacationRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->annualVacationRepository->generateNewYearlyRecords();

        return Command::SUCCESS;
    }
}
