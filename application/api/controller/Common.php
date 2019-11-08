<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Validate;

class Common extends Controller
{
    protected $req; //用来处理客户端传递过来的参数
    protected $validater; //用来验证数据/参数
    protected $params; //过滤后符合要求的参数

    //控制器下面方法所要接受参数的
    protected $rules = array(
        'Douyin' => array(
            'home' => array(
                'page' => 'number|max:100',
                'page_size' => 'number|max:5',
            ),
        ),
    );

    /**
     * [构造方法]
     * @return [type] [description]
     */
    protected function _initialize()
    {

        parent::_initialize();
        $this->req = Request::instance();

        //1. 检车请求时间是否超时
        //$this->checkTime($this->req->only(['time']));

        //2. 验证token
        //$this->checkToken($this->req->param());
        
        //3. 验证参数,返回成功过滤后的参数数组
        $this->params = $this->checkParams($this->req->param(true));

        //print_r($this->params);
    }

    //检测请求的时间是否超时
    public function checkTime($arr)
    {
        //$this->returnMsg(400, '请求超时!');
        if (!isset($arr['time']) || intval($arr['time']) <= 1) {
            $this->returnMsg(400, '时间戳不存在!');
        }
        if (time() - intval($arr['time']) > 10) {
            $this->returnMsg(400, '请求超时!');
        }
    }

    //验证token方法 (防止篡改数据)
    /*
    $arr: 全部请求参数
    return : json
     */
    protected function checkToken($arr)
    {
        //检测客户端是否传递过来token数据
        if (!isset($arr['token']) || empty($arr['token'])) {
            $this->returnMsg(400, 'token不能为空');
        }

        //这是客户端api传递过来的token
        $app_token = $arr['token'];

        //如果已经传递token数据，就删除token数据，生成服务端token与客户端的token做对比
        unset($arr['token']);

        $session_token = '';
        foreach ($arr as $key => $val) {
            $session_token .= md5($val);
        }

        $session_token = md5('api_' . $session_token . '_api');

        //echo $session_token;die; //调试输出

        //如果传递过来的token不相等
        if ($app_token !== $session_token) {
            $this->returnMsg(400, 'token值不正确');
        }
    }

    //检测客户端传递过来的其他参数（用户名，其他相关）
    /*
    param: $arr [除了time,token以外的其他参数]
    return:     [合格的参数数组]
     */
    protected function checkParams($arr)
    {
        //1.获取验证规则 (Array)
        $rule = $this->rules[$this->req->controller()][$this->req->action()];

        //2. 验证参数并且返回错误
        $this->validater = new Validate($rule);

        if (!$this->validater->check($arr)) {
            $this->returnMsg(400, $this->validater->getError());
        }

        //3. 如果正常，就通过验证

        return $arr;
    }

    //返回信息
    protected function returnMsg($code, $msg = '', $data = [])
    {
        $return_data['code'] = $code;
        $return_data['msg'] = $msg;
        $return_data['data'] = $data;

        echo json_encode($return_data);die;
    }




}
