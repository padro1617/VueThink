<?php
namespace app\index\controller;
use app\common\controller\Common;
use think\Request;

class Base extends Common
{
    public function _initialize()
    {
        parent::_initialize();
        header('Content-Type:application/html; charset=utf-8');
    }
}