<?php


namespace App\Entities;


class AwardRecord
{
    const NO_AWARD = 0;

    const AWARD_MOBILE      = 1;
    const AWARD_MOBILE_NAME = '手机';

    const AWARD_SIM      = 2;
    const AWARD_SIM_NAME = '电话卡';

    const AWARD_LABEL      = 3;
    const AWARD_LABEL_NAME = '贴纸';

    const AWARD_MAP = [
        self::AWARD_MOBILE => self::AWARD_MOBILE_NAME,
        self::AWARD_SIM    => self::AWARD_SIM_NAME,
        self::AWARD_LABEL  => self::AWARD_LABEL_NAME,
    ];


}
