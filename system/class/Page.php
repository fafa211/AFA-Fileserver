<?php
/**
 * 分页
 * @author     alfa
 * @version    1.0
 */

class Page
{
	/**
	 * 变量本身
	 *
	 * @var unknown_type
	 */
	public static $_this;
	
	/*
	* 每页显示记录类
	* @private integer
	*/
	private $psize;
	
	/*
	* 页码偏移量
	* @private integer
	*/
	private $pernum;
	
	/*
	* 要传递的变量数组
	* @private string
	*/
	private $privatestr = '';
	
	/*
	* 总页数
	* @private integer
	*/
	private $tpage = 0;
	
	/* 
	* 记录组总数
	* @private integer
	*/
	private $pers = 0;
	
	/*
	* 当前页码
	* @private integer
	*/
	private $page = 0;
	
	/* 
	* MySQL分页生成语句
	* @private string
	*/
	private $limit = '';
	private $start = '';
	private $end = 0;
	private $post = true;
	private $total = 0;    //记录总数
	private $last = false; //是否默认为最后一页
	private $text = '';    //分页内容

	//分页模式-前端样式
	private $mode = 1;

	/**
	 * 构造函数
	 *
	 * @param int $total  总记录数
	 * @param int $psize  单页记录数
	 * @param bool $last  是否默认显示最后一页
	 */
	public function __construct($total = 0, $psize = 0, $last = false){
		$this->total = $total;
		$this->psize = $psize?$psize:common::config('page.psize');	
		$this->pernum = common::config('page.pnum');
		$this->tpage = ceil($total / $this->psize);
		$this->pers = ceil($this->tpage / $this->pernum);
		$this->last = $last;
		$this->get();
	}
	
	/**
	 * 初始化实例
	 *
	 * @return object page object
	 */
	public static function instance($total = 0, $psize = 0, $last = false){
		if (!is_object(self::$_this)) self::$_this = new page($total, $psize, $last);
		return self::$_this;
	}
	
	/**
	* 取得传递变量精数组,并检测它是否为空
	* @param 最后一页 $tpage
	* @return v
	*/
	public function get()
	{
		$reqArr = array_merge($_GET, $_POST);
		$i=0; 
		foreach($reqArr as $k =>$v){
			$i++;
			$str = ($i==1) ? '?' : '&';
			if (!is_array($v))
			$this->privatestr = ($k<>'p') ? $this->privatestr.$str.$k.'='.htmlentities(urlencode($v)) : $this->privatestr;
		}
		$this->privatestr = input::uri('base').input::uri("path").($this->privatestr ? $this->privatestr.'&' : '?');
		$this->page = input::request('p') ? input::request('p')  : ($this->last ? $this->tpage : 1);
		
		// 用于 MySQL 分页生成语句 // 
		if ($this->page < $this->tpage ){
            $this->limit = ($this->page - 1)*$this->psize.','.$this->psize;
		} elseif ($this->page == $this->tpage) {
            $this->limit = ($this->page - 1) * $this->psize.','.($this->total - ($this->page -1) * $this->psize);
		} else {
		    $this->page = 1;
		    $this->limit = '0,'.$this->psize;
		}
		$this->start = ($this->page - 1) * $this->psize;
		$this->end = $this->start + $this->psize;
	}
	
    /**
     * @todo 分页提示信息
     * @param int $number:记录总条数
     * @return string $pageText：分页html
     */
	function showPageTip()
	{
	    if (($this->end) > $this->total)
	    {
	        $this->end = $this->total;
	    }
	    $text = "以下是 ". $this->total ." 个中的第 ". ($this->start+1) . "～". $this->end ." 个";
	    return $text;
	}
	
	/**
	 * 分页显示
	 */
	public function __toString(){
	    if ($this->total == 0) return $this->text;
        if ($this->mode == 1) return $this->modeOne();
        if ($this->mode == 2) return $this->modeTwo();
	}

	/**
	 * 分页模式 - 前端样式区别
	 */
	public function setMode($mode = 1){
		$this->setMode($mode);
	}

	/**
     * 模式1
     * 全面分页模式
	 * @return string 默认分页方式
	 */
    public function modeOne(){
        $setpage = $this->page ? ceil($this->page/$this->pernum) : 1;

        $pagenum = ($this->tpage > $this->pernum) ? $this->pernum : $this->tpage;

		$this->text = "共".$this->total."个 ";
		if ($this->tpage == 1){
			$this->text .= '共 1 页 当前页为第一页';
			return $this->text;
		}

		$this->text .= '共 '.$this->tpage . ' 页 ';
		if ($this->page > 1){//当前页大于第1页
			$pre = $this->page-1;
			if ($this->page > 5){//当前页大于第5页
				$this->text .= '<a href="'.$this->privatestr.'p=1" target="_self">&#171;第一页</a>';
			}
			$this->text .= '<a href="'.$this->privatestr.'p='.$pre.'" target="_self">上一页</a>';
		}
		$i = ($setpage-1)*$this->pernum;
		/**
		for($j=$i; $j<($i+$pagenum+5) && $j<$this->tpage; $j++) {
		$newpage = $j+1;
		if ($this->page == $j+1){
		$this->text .= '<span class="page_current">'.($j+1).'</span>';
		}else{
		$this->text .= '<a href="'.$this->privatestr.'p='.$newpage.'" target="_self">'.($j+1).'</a>';
		}
		}
		///* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next »
		 **/
		/**
		 * 总的this->tpage
		 * 当前this->page
		 *
		 */
		/* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */
		if($this->page<10&&$this->tpage<=10){
			$i=0;
			for($j=$i; $j<($i+$pagenum+3) && $j<$this->tpage; $j++) {
				$newpage = $j+1;
				if ($this->page == $j+1){
					$this->text .= '<span class="page_current">'.($j+1).'</span>';
				}else{
					$this->text .= '<a href="'.$this->privatestr.'p='.$newpage.'" target="_self">'.($j+1).'</a>';
				}
			}

		}

		if($this->page<10&&$this->tpage>10){
			$i=0;
			for($j=$i; $j<($i+$pagenum+3) && $j<$this->tpage; $j++) {
				$newpage = $j+1;
				if ($this->page == $j+1){
					$this->text .= '<span class="page_current">'.($j+1).'</span>';
				}else{
					$this->text .= '<a href="'.$this->privatestr.'p='.$newpage.'" target="_self">'.($j+1).'</a>';
				}
			}
			$this->text .= '&hellip;';
			$this->text .= '<a href="'.$this->privatestr.'p='.($this->tpage-1).'" target="_self">'.($this->tpage-1).'</a>';
			$this->text .= '<a href="'.$this->privatestr.'p='.$this->tpage.'" target="_self">'.($this->tpage).'</a>';
		}
		/* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */
		if($this->page>=10&&$this->page>$this->tpage-6){
			$this->text .= '<a href="'.$this->privatestr.'p='.(1).'" target="_self">'.(1).'</a>';
			$this->text .= '<a href="'.$this->privatestr.'p='.(2).'" target="_self">'.(2).'</a>';
			$this->text .= '&hellip;';
			$i = $this->tpage-6;
			for($j=$i; $j<($i+$pagenum+5) && $j<$this->tpage; $j++) {
				$newpage = $j+1;
				if ($this->page == $j+1){
					$this->text .= '<span class="page_current">'.($j+1).'</span>';
				}else{
					$this->text .= '<a href="'.$this->privatestr.'p='.$newpage.'" target="_self">'.($j+1).'</a>';
				}
			}
		}
		/* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26 ... 56 57 58  Next » */
		if($this->page>=10&&$this->page<=$this->tpage-6){
			$this->text .= '<a href="'.$this->privatestr.'p='.(1).'" target="_self">'.(1).'</a>';
			$this->text .= '<a href="'.$this->privatestr.'p='.(2).'" target="_self">'.(2).'</a>';
			$this->text .= '&hellip;';
			$i = $this->page-3;
			for($j=$i; $j<($i+$pagenum) && $j<$this->tpage; $j++) {
				$newpage = $j+1;
				if ($this->page == $j+1){
					$this->text .= '<span class="page_current">'.($j+1).'</span>';
				}else{
					$this->text .= '<a href="'.$this->privatestr.'p='.$newpage.'" target="_self">'.($j+1).'</a>';
				}
			}

			$this->text .= '&hellip;';
			$this->text .= '<a href="'.$this->privatestr.'p='.($this->tpage-1).'" target="_self">'.($this->tpage-1).'</a>';
			$this->text .= '<a href="'.$this->privatestr.'p='.$this->tpage.'" target="_self">'.($this->tpage).'</a>';

		}




		if ($this->page < $this->tpage){//当前页小于最大页
			$next = $this->page+1;
			$this->text .= '<a href="'.$this->privatestr.'p='.$next.'" target="_self">下一页</a>';
			$this->text .= '<a href="'.$this->privatestr.'p='.$this->tpage.'" target="_self">末页&#187;</a>';
		}

		return '<div class="page_class"><div class="page_class_bg">'.$this->text.'</div></div>';
	}

    /**
     * 模式2
     * 简要分页模式-前端样式
     * @return string
     */
    public function modeTwo(){
        if ($this->tpage == 1){
            return $this->text;
        }

        if($this->page > $this->tpage){
            $this->page = $this->tpage;
        }

        if ($this->page > 1){//当前页大于第1页
            $pre = $this->page-1;
            $this->text .= '<a href="'.$this->privatestr.'p='.$pre.'" class="downup" target="_self">上一页</a>';
        }

        //里面的页码
        for($i=1; $i<=$this->tpage ; $i++ ){
            if($i == $this->page){
                $this->text .= "<b>{$i}</b>";
            }else{
                $this->text .= '<a href="' . $this->privatestr . 'p=' . $i . '" target="_self">' . $i . '</a>';
            }
        }

        if ($this->page < $this->tpage) {//当前页大于第1页
            $next = $this->page + 1;
            $this->text .= '<a href="' . $this->privatestr . 'p=' . $next . '" class="downup" target="_self">下一页</a>';
        }

        return $this->text;
	}
	
	/**
	 * get vars
	 */
	public function __get($key){
		if (isset($this->$key)) return $this->$key;
	}
	
}

?>