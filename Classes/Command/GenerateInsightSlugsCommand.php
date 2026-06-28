<?php

declare(strict_types=1);

namespace Indiz\Products\Command;

/**
 * command in cli: vendor/bin/typo3 products:generate-insight-slugs
 */

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsCommand(
    name: 'products:generate-insight-slugs',
    description: 'Generates missing path_segment slugs for insight records',
)]
class GenerateInsightSlugsCommand extends Command
{
    private const TABLE = 'tx_products_domain_model_insight';
    private const FIELD = 'path_segment';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fieldConfig = $GLOBALS['TCA'][self::TABLE]['columns'][self::FIELD]['config'];
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, self::TABLE, self::FIELD, $fieldConfig);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);

        $records = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq(self::FIELD, $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull(self::FIELD)
                ),
                //$queryBuilder->expr()->eq('sys_language_uid', 0),
                $queryBuilder->expr()->eq('deleted', 0)
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE);

        foreach ($records as $record) {
            $slug = $slugHelper->generate($record, (int)$record['pid']);
            $state = RecordStateFactory::forName(self::TABLE)
                ->fromArray($record, (int)$record['pid'], (int)$record['uid']);
            $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
            if (str_ends_with($slug, '-1')) {
                $slug = substr($slug, 0, -2);
            }


            $connection->update(
                self::TABLE,
                [self::FIELD => $slug],
                ['uid' => $record['uid']]
            );

            $io->writeln(sprintf('  UID %d → %s', $record['uid'], $slug));
        }

        $io->success(sprintf('Generated slugs for %d records.', count($records)));
        return Command::SUCCESS;
    }
}
