<?php

namespace App\Services;

use App\Entities\AwardRecord;
use App\Entities\RedisMessage;
use App\Repositories\AwardRecordRepository;
use App\Repositories\CaptchaRepository;
use App\Repositories\ApplyRepository;
use Illuminate\Support\Facades\Redis;

class LuckyService
{
    protected $captchaRepository;

    protected $applyRepository;

    protected $awardRecordRepository;

    protected $kafka;

    public function __construct(CaptchaRepository $captchaRepository,
                                ApplyRepository $applyRepository,
                                AwardRecordRepository $awardRecordRepository,
                                $kafka)
    {
        $this->captchaRepository     = $captchaRepository;
        $this->applyRepository       = $applyRepository;
        $this->awardRecordRepository = $awardRecordRepository;
        $this->kafka                 = $kafka;
    }

    /**
     * 用户报名
     *
     * @param $phone
     * @param $text
     * @return mixed
     * @throws \Exception
     */
    public function apply($phone, $text)
    {
        if (!$this->canApply($phone)) {
            throw new \Exception('尊敬的用户，您已报名');
        }
        $this->kafka->send('topic1', json_encode(['phone' => $phone, 'text' => htmlspecialchars($text)]));
        return $this->captchaRepository->getCaptcha($phone);
    }

    /**
     * 判断用户是否还能报名
     *
     * @param $phone
     * @return mixed
     */
    protected function canApply($phone)
    {
        $offset = (int)substr($phone, 1);
        return $this->haveRecorded(RedisMessage::APPLY_KEY, $offset, RedisMessage::computeExpireTime()) ? false : true;
    }

    protected function haveRecorded($key1, $argv1, $argv2)
    {
        $lua = <<<lua
            local exit = redis.call("exits",KEYS[1]);
            if(exit==0) then
                redis.call("setBit",KEYS[1],ARGV[1],1);
                redis.call("expire",KEYS[1],ARGV[2]);
                return 0;
            end;
            return redis.call("setBit",KEYS[1],ARGV[1],1);

lua;
        $ret = Redis::eval($lua, 1, $key1, $argv1, $argv2);
        if ($ret) {
            return true;
        }

        return false;
    }

    /**
     * 抽奖
     *
     * @param $phone
     * @return bool|int
     * @throws \Exception
     */
    public function luckyDraw($phone)
    {
        if (!$this->canDraw($phone)) {
            throw new \Exception('尊敬的用户，您今天已经参与过抽奖');
        }

        $rand = rand(1, 100);
        if ($rand == 95) {
            return $this->drawMobile($phone);
        } elseif ($rand > 95) {
            return $this->drawSim($phone);
        } else {
            return $this->drawLabel($phone);
        }
    }

    /**
     * 判断当前用户是否能抽奖
     *
     * @param $phone
     * @return mixed
     */
    protected function canDraw($phone)
    {
        $offset = (int)substr($phone, 1);
        return $this->haveRecorded(sprintf(RedisMessage::PHONE_KEY, date('Y-m-d')), $offset, RedisMessage::PHONE_KEY_EXPIRES) ? false : true;
    }

    /**
     * 抽取手机
     *
     * @return bool
     */
    protected function drawMobile($phone)
    {
        $lua = <<<lua
            local down = redis.call("exits",KEYS[1]);
            if(down == 0) then
                redis.call("set",KEYS[1],1);
                redis.call("expire",KEYS[1],ARGV[1]);
                local num =  redis.call("incr",KEYS[2]);
                if(num == 1) then
                    redis.call("expire",KEYS[2],ARGV[2]);
                    return 1;
                elseif(num<=5) then
                    return 1;
                else
                    return 0;
                end;
            else
               return 0;
            end;
lua;
        $ret = Redis::eval($lua, 2,
            sprintf(RedisMessage::MOBILE_DOWN, date('Y-m-d')), RedisMessage::MOBILE_KEY,
            RedisMessage::MOBILE_DOWN_EXPIRES, RedisMessage::computeExpireTime());
        if ($ret) {
            $this->recordAward($phone, AwardRecord::AWARD_MOBILE);
            return AwardRecord::AWARD_MOBILE;
        }

        return AwardRecord::NO_AWARD;
    }

    /**
     * 抽取电话卡
     *
     * @param $phone
     * @return bool
     */
    protected function drawSim($phone)
    {
        $lua = <<<lua
            local exit = redis.call("exits",KEYS[1]);
            if(exit == 0) then
                redis.call("hincrby",KEYS[1],ARGV[1]);
                redis.call("expire",KEYS[1],ARGV[2]);
            else
                local times = redis.call("hincrby",KEYS[1],ARGV[1]);
                if(times > 2) then
                    return 0;
                end;
            end;

            local nums = redis.call("incr",KEYS[2]);
            if(nums == 1) then
                redis.call("expire",KEYS[2],ARGV[2]);
            elseif(nums>100) then
                return 0;
            end;

            return 1;
lua;
        $ret = Redis::eval($lua, 2, RedisMessage::SIM_PEOPLE, RedisMessage::SIM_KEY, $phone, RedisMessage::computeExpireTime());
        if ($ret) {
            $this->recordAward($phone, AwardRecord::AWARD_SIM);
            return AwardRecord::AWARD_SIM;
        }

        return AwardRecord::NO_AWARD;
    }

    /**
     * 抽取贴纸
     *
     * @param $phone
     * @return bool
     */
    protected function drawLabel($phone)
    {
        $this->recordAward($phone, AwardRecord::AWARD_LABEL);
        return AwardRecord::AWARD_LABEL;
    }

    /**
     * 写入奖品记录
     *
     * @param $phone
     * @param $award
     */
    protected function recordAward($phone, $award)
    {
        $this->kafka->send('topic2', json_encode(['phone' => $phone, 'award' => $award]));
    }


    /**
     * 获取报名记录
     *
     * @param $page
     * @param $pageSize
     * @return string
     * @throws \Exception
     */
    public function applyList($page, $pageSize)
    {
        return $this->applyRepository->list($page, $pageSize);
    }

    /**
     * 导出获奖记录
     *
     * @param $conditions
     * @return mixed
     * @throws \Exception
     */
    public function exportAward($conditions)
    {
        $list = $this->awardRecordRepository->list($conditions);
        foreach ($list as &$item) {
            $item['award'] = AwardRecord::AWARD_MAP[$item['award']];
        }
        // 上传到存储服务获取url
        return $url ?? '';
    }
}
