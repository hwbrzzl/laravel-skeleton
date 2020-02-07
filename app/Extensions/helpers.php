<?php

use App\Exceptions\GeneralException;
use App\Jobs\SendBearyChatJob;
use App\Models\BaseAdmin;
use App\Notifications\BusinessNotification;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\PhoneNumber;

if ( ! function_exists('user')) {
    function user($field = null)
    {
        //取到当前登录的管理员，pc 登录 或 api 登录
        return App::make('user');
    }
}

if ( ! function_exists('now')) {
    /**
     * @return \Carbon\Carbon
     */
    function now()
    {
        return \Carbon\Carbon::now();
    }
}

if ( ! function_exists('asset_v')) {
    /**
     * @param $jsSrc
     *
     * @return string
     *
     * @internal param $string
     */
    function asset_v($jsSrc)
    {
        return asset($jsSrc)."?v={$GLOBALS['version']}";
    }
}

if ( ! function_exists('carbon')) {
    /**
     * @param $dateString
     *
     * @return Carbon
     * @throws Exception
     */
    function carbon($dateString)
    {
        return new Carbon($dateString);
    }
}

if ( ! function_exists('is_money')) {
    function is_money($money)
    {
        return preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money);
    }
}

if ( ! function_exists('is_prod')) {
    function is_prod()
    {
        return config('app.env') == 'production';
    }
}

if ( ! function_exists('is_local')) {
    function is_local()
    {
        return config('app.env') == 'local';
    }
}

if ( ! function_exists('get_sn')) {
    /**
     * 获取订单号
     *
     * @param  null  $component
     *
     * @return string
     */
    function get_sn($component = null)
    {
        // 规则：2位产品识别，1位支付渠道，4位用户id后四位，4位时间戳后四位，3位随机数
        switch ($component) {
            case BaseAdmin::COMPONENT_WITHDRAW:
                $product_flag = '19';
                break;
            case BaseAdmin::COMPONENT_VIRTUAL:
                $product_flag = '20';
                break;
            case BaseAdmin::COMPONENT_ORDER:
                $product_flag = '11';
                break;
            default:
                $product_flag = '10';
        }

        // 支付类型，1：微信
        $pay_flag = 1;

        // 用户后4位
        $user_flag = user() ? substr('000'.user()->id, -4) : random(4);

        // 时间戳后缀
        $time_flag = substr(time(), -4);

        // 随机数
        $random_flag = random(3);

        return $product_flag.$pay_flag.$user_flag.$time_flag.$random_flag;
    }
}

if ( ! function_exists('get_sign')) {
    function get_sign(array $data, string $app_secret): string
    {
        // 获取参数的 key
        $keys = [];
        foreach ($data as $key => $value) {
            $keys[$key] = $key;
        }

        // 对 key 进行排序
        sort($keys);

        // 获取 key=value 的值
        $values = [];
        foreach ($keys as $k => $v) {
            $values[] = $keys[$k].'='.$data[$v];
        }

        // 获取需要加密的字符串
        $str = implode('&', $values);

        return strtoupper(md5($str.$app_secret));
    }
}
