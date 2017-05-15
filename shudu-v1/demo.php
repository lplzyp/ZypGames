<?php

	/**
	 *  此版本新增对角线数独模式，破解方法存在错误，暂不可用
	 *
	 * 	@author yanpengzhao 624476490@qq.com
	 *  @date( 2017/5/11 )
	 *
	 * 	该文件为demo，如需修改请到 shudu.php 文件基本实现了封装，不接受继承
	 *
	 *  数独形式如下：
	 *  	1 1 1 2 2 2 3 3 3
	 *  	1 1 1 2 2 2 3 3 3
	 *  	1 1 1 2 2 2 3 3 3
	 *  	x x x x x x x x x
	 *  	x x x x x x x x x
	 *  	x x x x x x x x x
	 *  	x x x x x x x x x
	 *  	x x x x x x x x x
	 *
	 * 规则：
	 * 		1，此为 9 * 9 ， 1 ， 2 ， 3 分别代表一个宫格 ，每个宫格只能填写0-9 ，且不能重复
	 * 		2，同行同列不能有重复数字
	 *
	 * ps: 	最近迷上了数独游戏，上地铁公交都在玩，所以做了这个东西，国人这个还是玩的不多，听说在日本很流行，基本挤地铁都在玩这个
	 *
	 *
	 *
	 * 	setModule() ** 设置模式 可切换传统模式与对角线数独模式
	 * 	clearShudu() ** 若需多次声称数独 ，在下次使用前需清除
	 * 	clearCrackShudu() ** 若需多次破解数独，在下次使用前需清除
	 */
	
	ini_set("display_errors" , 1);
	error_reporting(E_ALL);

	include("shudu.php");

	$shudu = new shudu(1);
	$shudu->generate();
	$shudu->draw();



	
 



?>