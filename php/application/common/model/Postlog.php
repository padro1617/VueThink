<?php
// +----------------------------------------------------------------------
// | Description: 岗位
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\model\Common;
use app\common\validate;

class Postlog extends Common
{

    /**
     * 为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     */
	protected $name = 'admin_postlog';
    protected $createTime = 'create_time';
    protected $updateTime = false;
	protected $autoWriteTimestamp = true;
	protected $insert = [
	];

	/**
	 * 写入日志
	 * @param     $uid
	 * @param     $postid
	 * @param int $splitsecond 间隔秒数
	 * @return false|\PDOStatement|string|\think\Collection
	 */
	public function add($uid,$postid)
	{
		//		'create_at' => (date('Y-m-d H:i:s',time()))
		//写入日志
		$params=array(
			'postid'=>$postid,
			'user_id'=>$uid,
			'create_time'=>time(),
			'create_at'=>(date('Y-m-d H:i:s',time()))
		);
		$this->startTrans();
		try {
			$this->insert($params);
			$this->commit();
			return true;
			//			return true;
		} catch(\Exception $e) {
			$this->rollback();
			$this->error = '添加失败：'.json_encode($params).'------'.$e->getMessage();
			return false;
		}
		return false;
	}
}