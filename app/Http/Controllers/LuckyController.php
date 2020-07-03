<?php


namespace App\Http\Controllers;

use App\Http\Requests\Apply;
use App\Http\Requests\ApplyList;
use App\Http\Requests\LuckyDraw;
use App\Services\LuckyService;
use Illuminate\Support\Facades\Response;

class LuckyController extends Controller
{

    protected $luckyService;

    public function __construct(LuckyService $luckyService)
    {
        $this->luckyService = $luckyService;
    }

    /**
     * 报名
     *
     * @param Apply $request
     * @return mixed
     */
    public function apply(Apply $request)
    {
        $phone   = $request->input('phone');
        $text    = $request->input('text');
        $captcha = $this->luckyService->apply($phone, $text);
        return Response::wrap($captcha);
    }

    /**
     * 抽奖
     *
     * @param LuckyDraw $request
     * @return mixed
     * @throws \Exception
     */
    public function luckyDraw(LuckyDraw $request)
    {
        $phone = $request->input('phone');
        $award = $this->luckyService->luckyDraw($phone);
        return Response::wrap($award);
    }

    /**
     * 获取报名记录
     *
     * @param ApplyList $request
     * @return mixed
     * @throws \Exception
     */
    public function applyList(ApplyList $request)
    {
        $page     = $request->input('page');
        $pageSize = $request->input('pageSize');
        $list     = $this->luckyService->applyList($page, $pageSize);
        return Response::wrap($list);
    }

    /**
     * 获取数据导出地址
     *
     * @param $conditions
     * @return mixed
     * @throws \Exception
     */
    public function exportAward($conditions)
    {
        $url = $this->luckyService->exportAward($conditions);
        return Response::wrap($url);
    }
}
