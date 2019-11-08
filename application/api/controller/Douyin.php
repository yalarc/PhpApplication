<?php
namespace app\api\controller;


class Douyin extends Common
{
	public $datas;
	
    public function index()
    {
        echo 'controller:douyin      function: index';
    }
    
    public function home()
    {
    	$this->datas = $this->params;
    	
    	
        if (!isset($this->datas['page_size'])) {
            $this->datas['page_size'] = 3;
        }

        if (!isset($this->datas['page'])) {
            $this->datas['page'] = 1;
        }
    	
    	$count = db('ayc_douyin')->count();
    	$page_count = ceil($count / $this->datas['page_size']);
    	$field = 'dy_id,dy_video';
    	$res = db('ayc_douyin')->field($field)->page($this->datas['page'], $this->datas['page_size'])->select();
    	
    	if($res === false){
    		$this->returnMsg(400,'服务器正忙，请稍后重试');
    	}elseif (empty($res)) {
    		$return_data['videos'] = $res;
    		$return_data['page_count'] = $page_count;
    		$this->returnMsg(200,"暂无数据",$return_data);
    	}else {
    		$return_data['videos'] = $res;
    		$return_data['page_count'] = $page_count;
    		$this->returnMsg(200,"成功",$return_data);
    	}
    	
    }
}
