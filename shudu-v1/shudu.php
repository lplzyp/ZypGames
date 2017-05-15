<?php


/**
 *  数独类
 *  目前还只是传统数独:
 *  	生成 9 * 9 数独  generate()
 *   	打印出数独 draw()
 *   	破解数独 crackShudu()
 *   	数独游戏 game()
 *   @author zhaoyanpeng 624476490@qq.com
 *   @date( 2017/5/11 )
 *  
 */
 class shudu{

 	// 数独 0-9
 	private $number;

 	/*传统数独 或 对角线数独 生成的数组*/
 	private $shudu;

 	/*传统数独 或 对角线数独 游戏的难度级别*/
 	private $level;

 	/*破解传统数独 或 对角线数独 生成的数组*/
 	private $crackShudu;

 	/*游戏补全数据*/
 	public $fixedArr;

 	/*破解补全的基础数据*/
 	private $crackBaseArr;

 	/*添加玩法模式*/
 	private $module;

 	/* 对角线数独左 */
 	private $diagonalConfLeft;

 	/* 对角线数独右 */
 	private $diagonalConfRight;

 	// 传统数独
 	const TRADITION_MODULE_RULE = 0;

 	// x数独 又名 对角线数独
 	const DIAGONAL_LINE_MODULE_RULE = 1;

 	// 迷你数独
 	const MINI_MODULE_RULE = 2;

 	// 锯齿数独
 	const JIGSAW_MODULE_RULE = 3;

 	// 超级数独
 	const SUPER_MODULE_RULE = 4;

	/**
	 *  初始化
	 */
 	public function __construct($module = null){
		$this->number = [1,2,3,4,5,6,7,8,9];
		$this->shudu = array();
		$this->crackShudu = array();
		$this->fixedArr = array();
		$this->level = [
			40 , 55 , 65
		];
		$this->module = isset( $module ) ? $module : 0;
		/* 对角线数独配置 */
		$this->diagonalConfLeft = [
			0,10,20,30,40,50,60,70,80
		];
		$this->diagonalConfRight = [
			8,16,24,32,40,48,56,64,72
		];

	}	

	/**
	 *  生成数独 入口
	 * @return [type] [dedscription]
	 */
	public function generate(){
		set_time_limit(0);
		$module = $this->getModule();
		/* 传统数独 与 对角线数独 可沿用同一套递归规则 */
		if(  $module ==  SELF::TRADITION_MODULE_RULE  )	
			$this->getNumRecursive();
		else if( $module == SELF::DIAGONAL_LINE_MODULE_RULE )
			$this->getDiagonalNumRecursive();
		
	}


	/**
	 *  总共是81个数字 全部压缩到同一个数组
	 *  规则 同一行 同一列 不能存在相同数字
	 *  一共有 9个 九宫格 
	 *  九宫格内不允许存在相同数字
	 * @param  integer $index [description]
	 * @return [type]         [description]
	 */
	private function getNumRecursive($index = 0){
		$bind_array = array();
		$relatedIndexArr = $this->getRelatedIndexArr($index);
		while( true ){
			$number = $this->returnRandNumber( $bind_array );
			if( $number == false ){
				return false;
			}
			if( !$this->checkNumRepeat( $relatedIndexArr , $number )  ){
				$this->shudu[$index] = $number;
				if( $index < 80 ){
					if( $this->getNumRecursive ( ++$index ) ){
						return true;
					}
				}else{
					return true;
				}

			}
			$this->clear($index);
			array_push($bind_array , $number );
		}

	}

	/**
	 *  递归生成对角线数独数据
	 *  与传统数独不同的是增加新的限定规则 双对角线 数字不能重复
	 * @param  integer $index [description]
	 * @return [type]         [description]
	 */
	private function getDiagonalNumRecursive($index = 0){
		$bind_array = array();
		$relatedIndexArr = $this->getDiagonalRelatedIndexArr($index);
		while( true ){
			$number = $this->returnRandNumber( $bind_array );
			if( $number == false ){
				return false;
			}
			if( !$this->checkDiagonalNumRepeat( $relatedIndexArr , $number )  ){
				$this->shudu[$index] = $number;
				if( $index < 80 ){
					if( $this->getDiagonalNumRecursive ( ++$index ) ){
						return true;
					}
				}else{
					return true;
				}

			}
			$this->clear($index);
			array_push($bind_array , $number );
		}		
	}




	/**
	 *  获取index位置所关联的所有数组下标集合
	 *  
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	private function getRelatedIndexArr($index){
		/*传统数独*/
		$relatedColsIndexArr = $this->getColsIndexArr($index);
		$relatedRowsIndexArr = $this->getRowsIndexArr($index);
		$relatedSameDistrictIndexArr = $this->getSameDistrictIndexArr($index);
		$relatedIndexArr = array_merge( $relatedRowsIndexArr , $relatedColsIndexArr , $relatedSameDistrictIndexArr  );
		$relatedIndexArr = array_unique($relatedIndexArr);
		return $relatedIndexArr;
	}

	/**
	 *   对角线数独 index下标对应的所有限定规则
	 * @param  [type] $indx [description]
	 * @return [type]       [description]
	 */
	private function getDiagonalRelatedIndexArr($index){
		$relatedTraditionIndexArr = $this->getRelatedIndexArr($index);
		$DiagonalIndexArr = $this->getDiagonalLineIndexArr($index);
		$relatedDiagonalIndexArr = array_merge( $relatedTraditionIndexArr  , $DiagonalIndexArr);
		$relatedDiagonalIndexArr = array_unique( $relatedDiagonalIndexArr );
		return $relatedDiagonalIndexArr;
	}	


	/**
	 *  返回对角线限定规则
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	private function getDiagonalLineIndexArr($index){
		if( $index < 0 || $index > 80 )
			throw new Exception('getDiagonalLineIndexArr Index out of range');
		if( !in_array( $index , $this->diagonalConfLeft ) && !in_array( $index , $this->diagonalConfRight ) )
			return array();
		if( $index == 40 )
			return array_merge( $this->diagonalConfLeft , $this->diagonalConfRight );
		if( in_array( $index , $this->diagonalConfLeft ) && !in_array( $index , $this->diagonalConfRight ) )
			return $this->diagonalConfLeft;
		if( !in_array( $index , $this->diagonalConfLeft ) && in_array( $index , $this->diagonalConfRight ) )
			return $this->diagonalConfRight;
		throw new Exception('getDiagonalLineIndexArr error ');
	}


	/**
	 *  返回与index处于同行的数组下标集合
	 *  
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	private function getRowsIndexArr($index){
		if( $index < 0 || $index > 80 )
			throw new Exception("getRowsIndexArr Index out of range");
		$rowsIndexArr = array();
		$suffix = $index / 9;
		$suffix = intval($suffix);
		for( $start = $suffix * 9 , $i = 0 ; $i < 9 ; $i++ , $start++ ){
			array_push( $rowsIndexArr , $start );
		}
		return $rowsIndexArr;
	}


	/**
	 *  返回与index处于同列的数组下标集合
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	private function getColsIndexArr($index){
		if( $index < 0 || $index > 80 )
			throw new Exception( "getColsIndexArr Index out of range" );
		$colsIndexArr = array();
		array_push( $colsIndexArr , $index );
		$suffix = $index / 9; // 处于第几行
		$suffix = intval($suffix);
		for( $i = $suffix , $tmp = $index ; $i > 0 ; $i-- ){
			$tmp -= 9;
			array_push( $colsIndexArr , $tmp );
		}
		for( $j = $suffix , $tmp = $index ; $j < 8 ; $j++  ){
			$tmp += 9;
			array_push( $colsIndexArr , $tmp );
		}
		sort($colsIndexArr);
		return $colsIndexArr;
	}

	/**
	 *   返回相同宫格内的数组下标集合
	 * @param  [type] $index [description]
	 * @return [type]        [description]. 
	 */
	private function getSameDistrictIndexArr($index){
		if( $index < 0 || $index > 80 )
			throw new Exception( "getSameDistrictIndexArr Index out of range" );
		$sameDistrictIndexArr = array();
		$suffix = $index / 9;
		$suffix = intval($suffix);
		$level = $suffix / 3;
		$level = intval($level);
		$level = $level * 3;
		$squareOne = $index - ( $suffix  * 9 );
		$squareOne = intval($squareOne / 3) * 3 ;
		$firstIndex = $level * 9  + $squareOne;
		for( $i = 0 ; $i < 3; $i++ , $firstIndex = $firstIndex + 9 ){
			$k = $firstIndex;
			for( $j = 0 ; $j < 3 ; $j++ ){
				array_push( $sameDistrictIndexArr , $k++ );
			}
		} 
		return $sameDistrictIndexArr;
	}


	/**
	 *  返回随机数字
	 * @param  [type] $bindArray [description]
	 * @return [type]            [description]
	 */
	private function returnRandNumber($bindArray = array() ){
		$optionalArr = $this->number;
		shuffle($optionalArr);
		if( !empty( $bindArray ) && is_array( $bindArray ) ){
			$optionalArr = array_diff( $optionalArr , $bindArray );
		}
		if( !empty($optionalArr) )
			return current($optionalArr);
		else
			return false;
	}

	/**
	 *  检测 数字是否有重复
	 *  
	 * @param  [type] $IndexArr 数组下标集合
	 * @param  [type] $number   [description]
	 * @return [type]           [description]
	 */
	private function checkNumRepeat($IndexArr , $number , $is_crack = false ){
		/*是否为破解模式*/
		if( $is_crack )
			$shudu = $this->crackShudu;
		else
			$shudu = $this->shudu;
		if( !is_array( $IndexArr ) || !isset($number ) || !is_array( $shudu ) )
			throw new Exception( 'checkNumRepeat indexArr or number is empty' );
		$relatedArr = array();
		foreach( $IndexArr as $v ){
			if( isset($shudu[$v]) && !empty($shudu[$v]) ){
				array_push( $relatedArr , $shudu[$v] );
			}
		}
		if( in_array( $number , $relatedArr ) )
			return true;
		else
			return false;
	}

	/**
	 *  检测 对角线数独 数字是否有重复
	 * @param  [type]  $indexArr [description]
	 * @param  [type]  $number   [description]
	 * @param  boolean $is_crack 是否为破解模式
	 * @return [type]            [description]
	 */
	private function checkDiagonalNumRepeat($indexArr , $number , $is_crack = false ){
		/*是否为破解模式*/
		if( $is_crack )
			$shudu = $this->crackShudu;
		else
			$shudu = $this->shudu;

		if ( $this->checkNumRepeat( $indexArr , $number , $is_crack ) )
			return true;

		/*不能存在对角线已有重复数字*/
		if( !is_array( $indexArr ) || !isset($number ) || !is_array( $shudu ) )
			throw new Exception( 'checkDiagonalNumRepeat indexArr or number is empty' );

		$diagLeftArr = array();
		$diagRightArr = array();

		if( isset( $shudu[79] ) )
			$shudu[80] = $number;

		foreach( $this->diagonalConfLeft as $v ){
			if ( isset( $shudu[$v] ) && !empty($shudu[$v]) )
				array_push($diagLeftArr , $shudu[$v] );
		}

		foreach( $this->diagonalConfRight as $v ){
			if ( isset( $shudu[$v] ) && !empty($shudu[$v]) )
				array_push($diagRightArr , $shudu[$v]);
		}

		if( 
			is_array( $diagRightArr ) 
			&& count($diagRightArr) == count(array_unique($diagRightArr) )
			&& is_array( $diagLeftArr ) 
			&& count($diagLeftArr) == count(array_unique($diagLeftArr) ) 
		)
			return false;
		else
			return true;
		

	}


	/**
	 *  clear 数组下标以及下标往后的所有的数据
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	private function clear($index , $is_crack = false){

		if( isset($index) && ( $index >= 0 || $index <= 80 )  ){
			$len = 80 - $index + 1;
			if( !$is_crack )
				array_splice( $this->shudu , $index   , $len  );
			else
				array_splice( $this->crackShudu , $index   , $len  ); //破解模式
		}else{
			throw new Exception('clear index out of range');
		}
		
	}


	/**
	 *  破解入口
	 * @param  [type] $incompleteShudu [description]
	 * @return [type]                  [description]
	 */
	public function crack($incompleteShudu){
		set_time_limit(0);
		$module = $this->getModule();
		if( $module == SELF::TRADITION_MODULE_RULE )
			$this->crackShudu($incompleteShudu);
		else if( $module == SELF::DIAGONAL_LINE_MODULE_RULE )
			$this->crackDiagShudu($incompleteShudu);
	}

	/**
	 *  破解 传统数独
	 * @param  [type] $incompleteShudu 残缺的数独数组
	 * @return [type]                  [description]
	 */
	private function crackShudu($incompleteShudu){
		if( !empty( $incompleteShudu ) && !is_array($incompleteShudu) )
			throw new Exception('crackShudu param empty or not array ');
		$this->crackBaseArr = $incompleteShudu;
		$comfirmedIndexArr = $this->getConfirmedIndexArr($incompleteShudu);
		$this->crackRecursive($comfirmedIndexArr);
	}

	/**
	 *  破解 对角线数独
	 * @param  [type] $incompleteShudu [description]
	 * @return [type]                  [description]
	 */
	private function crackDiagShudu($incompleteShudu){
		if( !empty( $incompleteShudu ) && !is_array($incompleteShudu) )
			throw new Exception('crackShudu param empty or not array');
		$this->crackBaseArr = $incompleteShudu;
		$comfirmedIndexArr = $this->getConfirmedIndexArr($incompleteShudu);
		$this->crackDiagRecursive($comfirmedIndexArr);
	}

	/**
	 *  破解 递归破解 对角线数独
	 * @param  [type]  $confirmedIndexArr [description]
	 * @param  integer $index             [description]
	 * @return [type]                     [description]
	 */
	private function crackDiagRecursive($confirmedIndexArr , $index = 0){
		$bind_array = array();
		$relatedIndexArr = $this->getDiagonalRelatedIndexArr($index);
		while( true ){
			if( in_array( $index , $confirmedIndexArr ) ){
				$this->crackShudu[$index] = $this->crackBaseArr[$index];
				if( $index == 80 && !$this->checkDiagonalNumRepeat( $relatedIndexArr , $this->crackBaseArr , true )){
					return true;
				}else if( $index == 80 && $this->checkDiagonalNumRepeat( $relatedIndexArr , $this->crackBaseArr , true ) ){
					return false;
				}
				if( $this->crackDiagRecursive( $confirmedIndexArr , ++$index ) )
					return true;
				else
					return false;
			}
			$num = $this->returnRandNumber($bind_array);
			if( $num == false )
				return false;
			if( !$this->checkDiagonalNumRepeat($relatedIndexArr , $num , true) ){
				$this->crackShudu[$index] = $num;
				if( $index < 80 ){
					if( $this->crackDiagRecursive( $confirmedIndexArr , ++$index ) )
						return true;
				}else{
					return false;
				}
			}
			array_push( $bind_array , $num );
			$this->clear( $index  , true );
		}
	}

	/**
	 *  递归破解传统数独
	 * [crack description]
	 * @return [type] [description]
	 */
	private function crackRecursive( $confirmedIndexArr  , $index = 0 ){
		$bind_array = array();
		$relatedIndexArr = $this->getRelatedIndexArr($index);
		while( true ){
			if( in_array( $index, $confirmedIndexArr ) ){
				$this->crackShudu[$index] = $this->crackBaseArr[$index];
				if( $index == 80  && !$this->checkNumRepeat( $relatedIndexArr , $num , true ) )
					return true;
				else if( $index == 80 && $this->checkNumRepeat( $relatedIndexArr , $num , true ) )
					return false;
				if( $this->crackRecursive(  $confirmedIndexArr , ++$index ) )
					return true;
				else
					return false;
			}
			$num = $this->returnRandNumber($bind_array);
			if( $num == false )
				return false;
			if( !$this->checkNumRepeat( $relatedIndexArr , $num , true ) ){
				$this->crackShudu[$index] = $num;
				if( $index < 80  ){
					if ( $this->crackRecursive(  $confirmedIndexArr , ++$index ) ){
						return true;
					}
				}else{
					return true;
				}
				
			}
			array_push($bind_array , $num);
			$this->clear(  $index , true);
		}
	}		


	/**
	 *  打印 数独 9 * 9
	 * @return [type] [description]
	 */
	public function draw($shudu = array()){
		if( empty($shudu) && !empty($this->shudu) )
			$shudu = $this->shudu;
		if( empty($shudu) && empty($this->shudu) )
			throw new Exception('shudu is empty');
		echo '<table style="width:500px ; height:500px; position:relative; top:50% ; left : 50% ;margin-left:-250px; margin-top : -250px ">';
		for( $i = 0 ; $i < 9 ; $i++ ){
			echo '<tr>';
			for( $j = 0; $j < 9  ; $j++ ){
				$index = $i * 9 + $j;
				if( isset($shudu[$index]) ){
					echo '<td>'.$shudu[$index].'</td>';
				}else{
					echo '<td>空</td>';
				}
				
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	/**
	 * 生成数独小游戏
	 * 填空的位置设置为 “空”
	 * @param  integer $level 游戏困难级别 ：0->初级（默认） 1->中级 2->高级
	 * @return [type]         [description]
	 */
	public function game($level = 0){
		$games = array_keys($this->level);
		if( !in_array( $level , $games ) )
			throw new Exception('此游戏难度尚未设置');
		if( empty($this->shudu) )
			throw new Exception( '请先 生成数独' );
		$emptyNum = $this->level[$level]; // 游戏的空位数量
		$emptyIndexArr = array_rand($this->shudu , $emptyNum);
		$shudu = $this->shudu;
		for( $i = 0 ; $i < $emptyNum ; $i++ ){
			$this->fixedArr[$emptyIndexArr[$i]] = $shudu[$emptyIndexArr[$i]];
			unset($shudu[$emptyIndexArr[$i]]);
		}
		$this->draw($shudu);

	}


	/**
	 *  获取 已固定的数组下标集合
	 * @param  [type] $shudu [description]
	 * @return [type]        [description]
	 */
	private function getConfirmedIndexArr( $shudu ){
		if( empty($shudu) || !is_array( $shudu ) )
			throw new Exception( "getConfirmedIndexArr Index out of range" );
		$indexArr = array_keys($shudu);
		return $indexArr;
	}

	/**
	 * 	game
	 *  返回需补全的数据
	 * @return [type] [description]
	 */
	public function returnFixedArr(){
		return $this->fixedArr;
	}

	/**
	 *  返回破解后的数据
	 * @return [type] [description]
	 */
	public function returnCrackArr(){
		return $this->crackShudu;
	}


	/**
	 *  返回生成的数独数据
	 * @return [type] [description]
	 */
	public function returnShuduArr(){
		return $this->shudu;
	}

	/**
	 * 设置数独模式（玩法）
	 * @param integer $module [description]
	 */
	public function setModule($module = 0){
		/*对角线*/
		if( $module == SELF::DIAGONAL_LINE_MODULE_RULE ){
			$this->module = SELF::DIAGONAL_LINE_MODULE_RULE;
		}
		/*迷你*/
		else if( $module == SELF::MINI_MODULE_RULE )
			$this->module = SELF::MINI_MODULE_RULE;
		/*锯齿*/
		else if( $module == SELF::JIGSAW_MODULE_RULE )
			$this->module = SELF::JIGSAW_MODULE_RULE;
		/*超级*/
		else if( $module == SELF::SUPER_MODULE_RULE )
			$this->module = SELF::SUPER_MODULE_RULE;
		/*传统*/
		else
			$this->module = SELF::TRADITION_MODULE_RULE;
	}

	/**
	 *  返回 数独模式
	 * @return [type] [description]
	 */
	public function getModule(){
		if( !isset($this->module) )
			$this->setModule();
		return $this->module;
	}

	/**
	 *  自由设置游戏难度
	 * @param [type] $level    [description]
	 * @param [type] $emptyNum [description]
	 */
	public function SetGameLevel($level , $emptyNum){
		 if( !isset( $level )  || !isset($emptyNum) )
		 	throw new Exception('设置游戏难度参数不能为空');
		 if( !is_integer($emptyNum) )
		 	throw new Exception('游戏难度设置空缺位置类型不对');
		 if( $emptyNum < 0 || $emptyNum > 81 )
		 	throw new Exception('空缺位置 只接受0-81');
		 $this->level[$level] = $emptyNum;
	}


	/**
	 *  清除 
	 * @return [type] [description]
	 */
	public function clearShudu(){
		$this->shudu = [];
	}

	/**
	 * 	清除 
	 * @return [type] [description]
	 */
	public function clearCrackShudu(){
		$this->crackShudu = [];
	}



 }



?>