<?php

namespace AppBundle\Command;

use AppBundle\Services\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppImportNotaryCommand command.
 */
class ImportNotaryCommand extends ContainerAwareCommand {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportService
     */
    private $importer;

    public function __construct(EntityManagerInterface $em, ImportService $importer, $name = null) {
        parent::__construct($name);
        $this->importer = $importer;
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure() {
        $this
            ->setName('app:import:notary')
            ->setDescription('Import notarial data from one or more CSV files')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of CSV files to import')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of header rows to skip', 1)
        ;
    }

    protected function import($file, $skip) {
        $handle = fopen($file, 'r');
        for ($i = 1; $i <= $skip; $i++) {
            fgetcsv($handle);
        }
        while ($row = fgetcsv($handle)) {
            $notary = $this->importer->findNotary($row[1]);
            $ledger = $this->importer->findLedger($notary, $row[2], $row[3]);
            $firstParty = $this->importer->findPerson($row[5], $row[4], $row[6], $row[7]);
            $secondParty = $this->importer->findPerson($row[11], $row[12], $row[13], $row[14]);
            $transaction = $this->importer->createTransaction($ledger, $firstParty, $secondParty, $row);
            $this->em->flush();
        }
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $files = $input->getArgument('files');
        $skip = $input->getOption('skip');
        foreach ($files as $file) {
            $this->import($file, $skip);
        }
    }

}