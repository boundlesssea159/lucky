<?php


namespace App\Repositories;


use Psr\Log\LoggerInterface;

class AwardRecordRepository
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function record($phone, $award)
    {
        try {
            return "insert into table (`phone`,`award`) values($phone,$award)";
        } catch (\Exception $e) {
            $this->logger->log('error', $e->getMessage());
            throw $e;
        }
    }

    public function list($conditions)
    {
        try {
            return "select `phone`,`award` from table where $conditions";
        } catch (\Exception $e) {
            $this->logger->log('error', $e->getMessage());
            throw $e;
        }
    }
}
