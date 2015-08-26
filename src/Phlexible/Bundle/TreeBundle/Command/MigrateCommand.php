<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Command;

use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migrate command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MigrateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tree:migrate')
            ->setDescription('Migrate nodes.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbal = $this->getContainer()->get('doctrine.dbal.default_connection');

        $dbal->query('UPDATE node n, node_mapped_field nmf SET n.backend_title = nmf.`backend` WHERE n.id = nmf.node_id AND n.content_version = nmf.version');
        $dbal->query('UPDATE node n, node_mapped_field nmf SET n.navigation_title = nmf.`navigation` WHERE n.id = nmf.node_id AND n.content_version = nmf.version AND n.navigation_title != "" AND n.navigation_title IS NOT NULL');
        $dbal->query('UPDATE node n, node_mapped_field nmf SET n.navigation_title = nmf.`backend` WHERE n.id = nmf.node_id AND n.content_version = nmf.version AND n.navigation_title = ""');
        $dbal->query('UPDATE node n, node_mapped_field nmf SET n.title = nmf.`page` WHERE n.id = nmf.node_id AND n.content_version = nmf.version');
        $dbal->query('UPDATE node n, node_mapped_field nmf SET n.title = nmf.`backend` WHERE n.id = nmf.node_id AND n.content_version = nmf.version AND n.title = ""');

        $dbal->query('UPDATE node n, element e SET n.content_version = e.latest_version WHERE n.content_type = "element"  AND n.content_id = e.eid');

        $dbal->query('UPDATE node SET backend_title = NULL WHERE backend_title = title OR backend_title = ""');
        $dbal->query('UPDATE node SET navigation_title = NULL WHERE navigation_title = title OR navigation_title = ""');
        $dbal->query('UPDATE node SET locale = "de"');

        $slugify = new Slugify();

        foreach ($dbal->fetchAll("SELECT * FROM node WHERE parent_id IS NULL") as $row) {
            $slug = $dbal->fetchColumn("SELECT hostname FROM siteroot WHERE id = '{$row['siteroot_id']}'");
            $slug = $slugify->slugify($slug);

            $dbal->query("UPDATE node SET slug = '$slug', path = '/$slug' WHERE id = {$row['id']}");

            $this->recurseNodes($row['id'], "/$slug");
        }

        return 0;
    }

    private function recurseNodes($parentId, $parentSlug)
    {
        $dbal = $this->getContainer()->get('doctrine.dbal.default_connection');
        $slugify = new Slugify();

        foreach ($dbal->fetchAll("SELECT * FROM node WHERE parent_id = $parentId") as $row) {
            $slug = $row['title'];
            $slug = $slugify->slugify($slug);
            $dbal->query("UPDATE node SET slug = '$slug', path = '$parentSlug/$slug' WHERE id = {$row['id']}");

            $this->recurseNodes($row['id'], "$parentSlug/$slug");
        }
    }
}

