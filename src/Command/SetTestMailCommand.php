<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:set-test-mail',
    description: 'Add a short description for your command',
)]
class SetTestMailCommand extends Command
{

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient->request('POST', "https://api.brevo.com/v3/smtp/email", [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
                'content-type' => 'application/json'
            ],
            'json' => [
                'sender' => [
                    'name' => 'Elias PODER',
                    'email' => 'elias.poder@gmail.com'
                ],
                'to' => [[
                    'email' => 'brightpuddi@gmail.com',
                    'name' => 'Bright PUDDI'
                ]],
                'subject' => 'Bonjour !',
                'htmlContent' => '<p>C\'est moi wsh</p>'
            ]
        ]);

        return Command::SUCCESS;
    }
}
