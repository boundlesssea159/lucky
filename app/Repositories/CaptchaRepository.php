<?php

namespace App\Repositories;

use Psr\Log\LoggerInterface;

class CaptchaRepository
{
    protected $client;

    protected $logger;

    public function __construct($client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * 验证码获取
     *
     * @param $phone
     * @return mixed
     * @throws \Exception
     */
    public function getCaptcha($phone)
    {
        try {
            return $this->client->captcha($phone);
        } catch (\Exception $exception) {
            $this->logger->log('error', $exception->getMessage());
            throw $exception;
        }
    }

}
