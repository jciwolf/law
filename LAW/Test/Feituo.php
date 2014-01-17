<?php
/**
 * @DESCRIPTION
 *
 *
 * type == 2
 * http://xp.gaopeng.cn:8002/test/callback?type=2&status=0&date=2013年12月30日&fwtype=0
 * http://xp.gaopeng.cn:8002/test/callback?type=2&status=1&date=2013年12月30日&fwtype=1
 * http://xp.gaopeng.cn:8002/test/callback?type=2&status=2&date=2013年12月30日&fwtype=0
 *
 *
 * type == 4
 * http://xp.gaopeng.cn:8002/test/callback?type=4&card=9986&actype=1&points=100&userpoints=480
 * http://xp.gaopeng.cn:8002/test/callback?type=4&card=9986&actype=2&points=100&userpoints=480
 *
 * type == 5
 * http://xp.gaopeng.cn:8002/test/callback?type=5&points=100&year=2013&month=7&day=10
 *
 * type == 6
 * http://xp.gaopeng.cn:8002/test/callback?type=6&cardtype=1&year=2013&month=12&day=23
 * http://xp.gaopeng.cn:8002/test/callback?type=6&cardtype=2&year=2013&month=12&day=23
 *
 * type == 7
 * http://xp.gaopeng.cn:8002/test/callback?type=7&cardtype=1&year=2013&month=12&day=23&fee=6203&times=6
 * http://xp.gaopeng.cn:8002/test/callback?type=7&cardtype=2&year=2013&month=12&day=23&fee=6203&times=6
 *
 * type == 8
 * http://xp.gaopeng.cn:8002/test/callback?type=8&cardtype=1&year=2013&month=12&day=23&fee=6203&times=6
 * http://xp.gaopeng.cn:8002/test/callback?type=8&cardtype=2&year=2013&month=12&day=23&fee=6203&times=6
 *
 * @MODIFY
 * 13-11-15 下午3:26 create file
 *
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Test_Feituo extends System_Lib_Controller
{
    private $method = array(
        'handler',
        'callback',
    );

    public function handlerAction()
    {
        $method = System_Lib_App::app()->get('method', System_Lib_Request::TYPE_STRING);
        if(!in_array($method, $this->method)) {
            return;
        }

        $this->$method();
    }

	private function handler()
	{

		$time = time();
		$params = array('status'=>'1', 'ver'=>'1', 'tm'=>(string)$time, 'type'=>'1', 'openid'=>'12121212');
		$md5key = 'M*)y!Qu0Tm-3';
		$arr1 = array_merge($params, array('md5key'=>$md5key));
		ksort($arr1);
		$str = join('', $arr1);
		$sign = md5($str);
		$params['sign'] = $sign;
		$cdata = array('cdata'=>json_encode($params));
		print_r($cdata);
		echo '<br />';
		$url = 'http://xp.weituangou.mobi/fracta-chevrolet/notify/callback';
		$url .= '?' . http_build_query($cdata);

		echo '<a href="'. $url . '" target="_blank">test</a>';
	}

    private  function callback()
	{
        $openid = System_Lib_App::app()->get('openid', System_Lib_Request::TYPE_STRING, 'oezz9totPm69KeR1GOG6N9ajPp34');
        $type   = System_Lib_App::app()->get('type', System_Lib_Request::TYPE_INT, 10);
		$time   = time();
		$md5key = 'M*)y!Qu0Tm-3';
        //$openid = 'oezz9tn5CQJNCAKM5kEkLqG08fdw';
        //$openid = 'oezz9toTyrA4bVv7EbxdSeUYCzPM';
        $openid = 'oezz9tj60oM8oyMvbI_qshtj6Nss';
		$arr    = array('ver'=>1.0, 'tm'=>$time, 'openid'=>$openid, 'type'=>$type);

        switch($type)
        {
            case 2: // 预约服务
                $status = System_Lib_App::app()->get('status', System_Lib_Request::TYPE_INT, 0);
                $date   = System_Lib_App::app()->get('date', System_Lib_Request::TYPE_STRING, '');
                $fwtype = System_Lib_App::app()->get('fwtype', System_Lib_Request::TYPE_INT, 0);
                $arr['fwtype']  = $fwtype;
                $arr['status']  = $status;
                $arr['reservetime']    = $date;
                break;
            case 4: // 积分交易
                $card       = System_Lib_App::app()->get('card', System_Lib_Request::TYPE_STRING, '');
                $actype     = System_Lib_App::app()->get('actype', System_Lib_Request::TYPE_INT, 1);
                $points     = System_Lib_App::app()->get('points', System_Lib_Request::TYPE_INT, 0);
                $userpoints = System_Lib_App::app()->get('userpoints', System_Lib_Request::TYPE_INT, 0);
                $arr['card']   = $card;
                $arr['actype'] = $actype;
                $arr['points'] = $points;
                $arr['userpoints'] = $userpoints;
                break;
            case 5: // 积分到期提醒
                $points = System_Lib_App::app()->get('points', System_Lib_Request::TYPE_INT, 0);
                $year   = System_Lib_App::app()->get('year', System_Lib_Request::TYPE_STRING, '');
                $month  = System_Lib_App::app()->get('month', System_Lib_Request::TYPE_STRING, '');
                $day    = System_Lib_App::app()->get('day', System_Lib_Request::TYPE_STRING, '');
                $arr['points'] = $points;
                $arr['year']  = $year;
                $arr['month'] = $month;
                $arr['day']   = $day;
                break;
            case 6: // 会员升级提醒
                $cardtype   = System_Lib_App::app()->get('cardtype', System_Lib_Request::TYPE_INT, 1);
                $year       = System_Lib_App::app()->get('year', System_Lib_Request::TYPE_INT, 0);
                $month      = System_Lib_App::app()->get('month', System_Lib_Request::TYPE_INT, 0);
                $day        = System_Lib_App::app()->get('day', System_Lib_Request::TYPE_INT, 0);
                $arr['cardtype']    = $cardtype;
                $arr['year']        = $year;
                $arr['month']       = $month;
                $arr['day']         = $day;
                break;
            case 7: // 会员保级提醒
                $cardtype   = System_Lib_App::app()->get('cardtype', System_Lib_Request::TYPE_INT, 1);
                $times      = System_Lib_App::app()->get('times', System_Lib_Request::TYPE_INT, 1);
                $fee        = System_Lib_App::app()->get('fee', System_Lib_Request::TYPE_INT, 1);
                $year       = System_Lib_App::app()->get('year', System_Lib_Request::TYPE_INT, 0);
                $month      = System_Lib_App::app()->get('month', System_Lib_Request::TYPE_INT, 0);
                $day        = System_Lib_App::app()->get('day', System_Lib_Request::TYPE_INT, 0);
                $arr['cardtype']    = $cardtype;
                $arr['times']       = $times;
                $arr['fee']         = $fee;
                $arr['year']        = $year;
                $arr['month']       = $month;
                $arr['day']         = $day;
                break;
            case 8: // 会员保级提醒
                $cardtype   = System_Lib_App::app()->get('cardtype', System_Lib_Request::TYPE_INT, 1);
                $times      = System_Lib_App::app()->get('times', System_Lib_Request::TYPE_INT, 1);
                $fee        = System_Lib_App::app()->get('fee', System_Lib_Request::TYPE_INT, 1);
                $year       = System_Lib_App::app()->get('year', System_Lib_Request::TYPE_INT, 0);
                $month      = System_Lib_App::app()->get('month', System_Lib_Request::TYPE_INT, 0);
                $day        = System_Lib_App::app()->get('day', System_Lib_Request::TYPE_INT, 0);
                $arr['cardtype']    = $cardtype;
                $arr['times']       = $times;
                $arr['fee']         = $fee;
                $arr['year']        = $year;
                $arr['month']       = $month;
                $arr['day']         = $day;
                break;
        }

		$tmp = array_merge($arr, array('md5key'=>$md5key));
		ksort($tmp);
		$str = join('', $tmp);
		$sign = md5($str);
		$arr['sign'] = $sign;

		$cdata = json_encode($arr);
		$cdata = urlencode($cdata);

		//echo '<a href="http://xp.weituangou.mobi/notify/callback?cdata=' . $cdata . '" target="_blank">ClickMe</a>';
        print_r($arr);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo '<a href="http://xp.gaopeng.cn:8002/fracta-chevrolet/notify/callback?cdata=' . $cdata . '" target="_blank">ClickMe</a>';
	}

    private function encode()
	{
		$key = 'uiZ+v]HDLM*-VI)GjOg4Ua[g';
		$cdata     = array('openid' => 123456);
		$jdata = json_encode($cdata);
		print_r($jdata);
		echo '<br />';
		$data  = System_Lib_AES::encrypt($jdata, $key);
		$data  = System_Lib_Utils::base64_url_encode($data);

		print_r($data); // _11YjOwjyPllMD-fMiphy_osvABrgFN2Fwzswrvngmk.
	}

    private function decode()
	{
		$key = 'uiZ+v]HDLM*-VI)GjOg4Ua[g';

		$cdata     = 'Tlj4xe_OFqP8EWsDIFlz_AwxvlFfulCrTMvOThHuzOQ.';
		//$cdata     = strtr($cdata, '+/=', '-_.');
		print_r($cdata);
		echo '<br />';
		$cdata  = System_Lib_Utils::base64_url_decode($cdata);
		$data  = System_Lib_AES::decrypt($cdata, $key);
		$jdata = json_decode($data,true);
		print_r($jdata);

	}

    private function sign()
	{
		$arr = array(
			'ver'=>1.0,
			'tm' =>time(),
			'openid' => '123456789',
			'type' => 1,
		);
		echo json_encode($arr) . "\n";
		echo "<br />";
		$arr1 = array_merge($arr, array('md5key'=> 'M*)y!Qu0Tm-3'));
		echo json_encode($arr1) . "\n";
		echo "<br />";
		ksort($arr1);
		echo json_encode($arr1) . "\n";
		echo "\n";
		echo "<br />";
		$str1 = join('', $arr1);
		echo $str1 . "\n";
		echo "<br />";
		$sstr1 = md5($str1);
		echo $sstr1 . "\n";
		echo "<br />";
	}

}