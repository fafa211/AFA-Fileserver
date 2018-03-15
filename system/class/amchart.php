<?php 

    require_once DRIVERPATH.'chart/ChartConfig.php';
    require_once DRIVERPATH.'chart/Point.php';
    require_once DRIVERPATH.'chart/DataSet.php';
    require_once DRIVERPATH.'chart/XYDataSet.php';
    require_once DRIVERPATH.'chart/XYSeriesDataSet.php';
    
    require_once DRIVERPATH.'chart/view/primitive/Padding.php';
    require_once DRIVERPATH.'chart/view/primitive/Rectangle.php';
    require_once DRIVERPATH.'chart/view/primitive/Primitive.php';
    require_once DRIVERPATH.'chart/view/text/Text.php';
    require_once DRIVERPATH.'chart/view/color/Color.php';
    require_once DRIVERPATH.'chart/view/color/ColorSet.php';
    require_once DRIVERPATH.'chart/view/color/Palette.php';
    require_once DRIVERPATH.'chart/view/axis/Bound.php';
    require_once DRIVERPATH.'chart/view/axis/Axis.php';
    require_once DRIVERPATH.'chart/view/plot/Plot.php';
    require_once DRIVERPATH.'chart/view/caption/Caption.php';
    require_once DRIVERPATH.'chart/view/chart/Chart.php';
    require_once DRIVERPATH.'chart/view/chart/BarChart.php';
    require_once DRIVERPATH.'chart/view/chart/VerticalBarChart.php';
    require_once DRIVERPATH.'chart/view/chart/HorizontalBarChart.php';
    require_once DRIVERPATH.'chart/view/chart/LineChart.php';
    require_once DRIVERPATH.'chart/view/chart/PieChart.php';
    
 class amchart{

	public static function piechart($data=array(),$file='pie',$useragents=null){
		$chart = new PieChart(430,200);
		$dataSet = new XYDataSet();
		foreach ($data as $k=>$v){
			$dataSet->addPoint(new Point($k."(".$v.")", $v));
		}
		$chart->setDataSet($dataSet);
		$chart->setTitle($useragents);
		$chart->render(DOCROOT."images/chart/".$file.".png");
	}
}
// End Kohana_Cache
