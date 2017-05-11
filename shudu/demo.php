<?php


	/**
	 *  以下均为 传统数独 ，并不包括 x 数独 和 其它模式 
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
	 * ps: 最近迷上了数独游戏，上地铁公交都在玩，所以做了这个东西，国人这个还是玩的不多，听说在日本很流行，基本挤地铁都在玩这个
	 *  	
	 */
	
	ini_set("display_errors" , 1);
	error_reporting(E_ALL);

	// include("shudu.php");

	$shudu = new shudu();

	/*生成数独*/
	$shudu->generate();
	$shuduArr = $shudu->returnShuduArr();

	/*表格打印出数独*/
	$shudu->draw($shuduArr);

	/*生成数独游戏*/
	$shudu->SetGameLevel(4 , 50);  // 自由设置游戏难度，参数1无限制，对应下面的参数，参数2为0-81的数字，代表空缺的数量
	$shudu->game(4);   // 自动打印出表格形式， css 样式较为固定 引用的draw方法，两次draw之后打印出的数据为重叠

	/*数独游戏补全的信息*/
	$fixedArr = $shudu->returnFixedArr();

	
	/* 
		破解数独
		基础数据 由一唯数组组成的81个数字信息
		数组键名	 代表数独的位置
		数组键值   代表数独的值
	*/

	/* 破解需要传递参数 可以单独测试 */

	$baseArr = [];

	$shudu->crackShudu($baseArr);

	$crackArr = $shudu->returnCrackArr();  // 破解生成的数据

	$shudu->draw($crackArr); // 可打印出破解后的表格形式
 



?>