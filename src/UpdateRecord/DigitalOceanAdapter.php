<?php

namespace DnsUpdater\UpdateRecord;

use DigitalOceanV2\Api\DomainRecord as DomainRecordApi;
use DigitalOceanV2\DigitalOceanV2;
use DigitalOceanV2\Entity\DomainRecord;
use DnsUpdater\Record;

final class DigitalOceanAdapter implements UpdateRecord
{
    const NAME = 'digitalocean';

    /**
     * @var DomainRecord
     */
    private $domainRecords;

    /**
     * @var DomainRecordApi
     */
    private $domainRecordApi;

    /**
     * @param DigitalOceanV2 $digitalOceanApi
     */
    public function __construct(DigitalOceanV2 $digitalOceanApi)
    {
        $this->domainRecordApi = $digitalOceanApi->domainRecord();
    }

    /**
     * @param Record $record
     *
     * @return Record
     */
    public function persist(Record $record): Record
    {
        $domainId = $this->fetchDomainId($record->getDomain(), $record->getName());

        if (null === $domainId) {
            $this->domainRecordApi->create(
                $record->getDomain(),
                Record::TYPE_ADDRESS,
                $record->getName(),
                $record->getValue()
            );

            return $record;
        }

        $this->domainRecordApi->updateData($record->getDomain(), $domainId, (string) $record->getValue());

        return $record;
    }

    /**
     * @param string $domainName
     * @param string $recordName
     *
     * @return int|null
     */
    private function fetchDomainId(string $domainName, string $recordName): ?int
    {
        if (!isset($this->domainRecords[$domainName])) {
            $this->domainRecords[$domainName] = $this->domainRecordApi->getAll($domainName);
        }

        foreach ($this->domainRecords[$domainName] as $domainRecord) {
            if ($recordName === $domainRecord->name && Record::TYPE_ADDRESS === $domainRecord->type) {
                return $domainRecord->id;
            }
        }

        return null;
    }
}
