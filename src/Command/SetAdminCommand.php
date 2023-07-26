<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:set-admin',
    description: 'Add a short description for your command',
)]
class SetAdminCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->addArgument('userEmail', InputArgument::REQUIRED, 'Email de l\'utilisateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userEmail = $input->getArgument('userEmail');
        $tbl = ['email' => $userEmail];

        $userEntity = $this->userRepository->findOneBy($tbl);
        if ($userEntity !== null) {
            $userEntity->setRoles(["ROLE_ADMIN"]);

            $this->entityManager->persist($userEntity);
            $this->entityManager->flush();

            $output->writeln("L'utilisateur " . $userEmail . " est maintenat Admin");
            return Command::SUCCESS;
        } else {
            $output->writeln("L'utilisateur " . $userEmail . " n'existe pas");
            return Command::FAILURE;
        }
    }
}
