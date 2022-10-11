<?php

declare(strict_types=1);

namespace ProgrammerCat\MessageQueue\Plugin\ResourceModel;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\MessageQueue\Lock\WriterInterface;

class Lock
{
    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @param WriterInterface $writer
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        WriterInterface $writer,
        DeploymentConfig $deploymentConfig
    )
    {
        $this->writer = $writer;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * When maintenance mode is turned off, lock queue should be cleared
     *
     * @param MaintenanceMode $subject
     * @param boolean $result
     * @return void
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function afterSet(MaintenanceMode $subject, $result)
    {
        if ($this->deploymentConfig->isAvailable() && !$subject->isOn() && $result) {
            $this->writer->releaseOutdatedLocks();
        }
    }
}
