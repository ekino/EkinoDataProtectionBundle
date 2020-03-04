<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/data-protection-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\DataProtectionBundle\Command;

use Ekino\DataProtectionBundle\Meta\AnonymizedMetadataBuilder;
use Ekino\DataProtectionBundle\Meta\AnonymizedMetadataValidator;
use Ekino\DataProtectionBundle\QueryBuilder\AnonymizedQueryBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class AnonymizeDataCommand
 *
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 */
final class AnonymizeDataCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'ekino-data-protection:anonymize';

    protected $anonymizedMetadataBuilder;

    protected $anonymizedMetadataValidator;

    protected $anonymizedQueryBuilder;

    public function __construct(
        AnonymizedMetadataBuilder $anonymizedMetadataBuilder,
        AnonymizedMetadataValidator $anonymizedMetadataValidator,
        AnonymizedQueryBuilder $anonymizedQueryBuilder
    )
    {
        parent::__construct();

        $this->anonymizedMetadataBuilder   = $anonymizedMetadataBuilder;
        $this->anonymizedMetadataValidator = $anonymizedMetadataValidator;
        $this->anonymizedQueryBuilder      = $anonymizedQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Anonymize database based on entities annotations')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Set this parameter to execute this action')
            ->setHelp('Usage: `bin/console ekino-data-protection:anonymize`')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('<info>Anonymization starts</info>'));

        $anonymizedMetadatas = $this->anonymizedMetadataBuilder->build();
        $queries             = [];

        foreach ($anonymizedMetadatas as $anonymizedMetadata) {
            $this->anonymizedMetadataValidator->validate($anonymizedMetadata);
            $queries[] = $this->anonymizedQueryBuilder->buildQuery($anonymizedMetadata);
        }

        if (!$input->getOption('force')) {
            $output->writeln('<error>ATTENTION:</error> This operation should not be executed in a production environment.');
            $output->writeln('');
            $output->writeln('<info>Would annoymize your database according to your configuration.</info>');
            $output->writeln('Please run the operation with --force to execute');
            $output->writeln('<error>Some data will be lost/anonymized!</error>');

            $this->displayQueries($queries, $output);

            return 0;
        }

        $question = 'Are you sure you wish to continue & anonymize your database? (y/n)';

        if (! $this->canExecute($question, $input, $output)) {
            $output->writeln('<error>Anonymization cancelled!</error>');

            return 1;
        }

        $this->displayQueries($queries, $output);
        // @todo execute queries
        $output->writeln(sprintf('<info>Anonymization ends</info>'));

        return 0;
    }

    private function displayQueries(array $queries, OutputInterface $output): void
    {
        $output->writeln('<error>Following queries have been built and will be executed:</error>');

        foreach ($queries as $query) {
            $output->writeln(sprintf('<info>%s</info>', $query));
        }
    }

    private function askConfirmation(string $question, InputInterface $input, OutputInterface $output): bool
    {
        return $this->getHelper('question')->ask($input, $output, new ConfirmationQuestion($question));
    }

    private function canExecute(string $question, InputInterface $input, OutputInterface $output ): bool
    {
        return ! $input->isInteractive() || $this->askConfirmation($question, $input, $output);
    }
}
