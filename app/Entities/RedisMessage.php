<?php


namespace App\Entities;


class RedisMessage
{

    // 用户报名记录
    const APPLY_KEY = 'apply-key';

    // 每天参与抽奖的用户记录
    const PHONE_KEY         = 'phone-key:%s';
    const PHONE_KEY_EXPIRES = 24 * 60 * 60;

    // 奖品：手机
    const MOBILE_KEY = 'mobile-key';

    // 标记当日手机是否已抽取
    const MOBILE_DOWN         = 'mobile-down:%s';
    const MOBILE_DOWN_EXPIRES = 24 * 60 * 60;

    // 奖品：电话卡
    const SIM_KEY = 'sim-key';

    // 电话卡获奖记录
    const SIM_PEOPLE = 'sim-people';


    public static function computeExpireTime()
    {
        return strtotime('活动结束时间') - time();
    }
}
