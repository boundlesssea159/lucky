<?php


namespace App\Repositories;


use Psr\Log\LoggerInterface;

class ApplyRepository
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

//    public function findByPhone($phone)
//    {
//        try {
//            return "select `phone` from `table` where `phone` = $phone";
//        } catch (\Exception $exception) {
//            $this->logger->log('error', $exception->getMessage());
//            throw $exception;
//        }
//    }

    public function storeText($phone, $text)
    {
        try {
            return "update `table` set `text` = $text where `phone` = $phone";  // phone设置唯一键
        } catch (\Exception $exception) {
            $this->logger->log('error', $exception->getMessage());
            throw $exception;
        }
    }

    public function list($page, $pageSize)
    {
        try {
            $begin = $page * $pageSize;
            return "select `phone`,`text` from table limit($begin,$pageSize)";
        } catch (\Exception $exception) {
            $this->logger->log('error', $exception->getMessage());
            throw $exception;
        }
    }
}
