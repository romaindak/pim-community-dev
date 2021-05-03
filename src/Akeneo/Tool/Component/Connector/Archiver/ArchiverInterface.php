<?php

namespace Akeneo\Tool\Component\Connector\Archiver;

use Akeneo\Tool\Component\Batch\Model\JobExecution;

/**
 * Define an archiver
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @see       \Akeneo\Tool\Bundle\ConnectorBundle\EventListener
 */
interface ArchiverInterface
{
    /**
     * Archive a job execution
     *
     * @param JobExecution $jobExecution
     */
    public function archive(JobExecution $jobExecution): void;

    /**
     * Check if the job execution is supported
     *
     * @param JobExecution $jobExecution
     *
     * @return bool
     */
    public function supports(JobExecution $jobExecution): bool;

    /**
     * Get the archives of a job execution
     *
     * @param JobExecution $jobExecution
     * @param bool $deep whether archives are listed recursively
     *
     * @return string[]
     */
    public function getArchives(JobExecution $jobExecution, bool $deep = false): iterable;

    /**
     * Get a specific archive of a job execution
     *
     * @param JobExecution $jobExecution
     * @param string       $key
     *
     * @return resource
     */
    public function getArchive(JobExecution $jobExecution, string $key);

    /**
     * Get the archiver name
     *
     * @return string
     */
    public function getName(): string;
}
