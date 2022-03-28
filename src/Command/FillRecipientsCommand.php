<?php

namespace App\Command;

use App\Entity\Recipient;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fill',
    description: 'Fills recipients.',
    hidden: false,
    aliases: ['app:fill']
)]
class FillRecipientsCommand extends Command
{
    protected static $defaultName = 'app:fill';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = new SplFileObject(dirname(__DIR__, 2) . '/migrations/emails.csv', 'r');
        $file->seek(PHP_INT_MAX);
        $size = $file->key() + 1;

        $file = fopen(dirname(__DIR__, 2) . '/migrations/emails.csv', 'r');
        $counter = 0;
        $progressBar = new ProgressBar($output, $size/1000);

        while (($line = fgetcsv($file)) !== false) {
            $recipient = new Recipient();
            $recipient->setName($line[0] ?? null);
            $recipient->setEmail($line[1]);
            $recipient->setAge((int)$line[2]);
            $recipient->setCity($line[3] ?? null);

            $this->entityManager->persist($recipient);
            if ($counter == 999) {
                $this->entityManager->flush();
                $counter = 0;
                $progressBar->advance();
            } else {
                $counter++;
            }
        }
        $this->entityManager->flush();

        fclose($file);

        return Command::SUCCESS;
    }


    private function fill()
    {

    }
}