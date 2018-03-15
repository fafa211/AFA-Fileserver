<?php

/**
 * Parses the PHPDoc comments for metadata. Inspired by Documentor code base
 * @category   Framework
 * @package    restler
 * @subpackage helper
 * @author     Murray Picton <info@murraypicton.com>
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://github.com/murraypicton/Doqumentor
 */
class docparse
{

    private static $p;

    private $params = array();

    public static function getInstance($flag = true){
        if(!$flag){
            self::$p = new docparse ();
        }
        if(self::$p == null){
            self::$p = new docparse ();
        }

        return self::$p;
    }

    /**
     * @param string $doc 反射得到的类或方法注释内容
     * @return array 解析结果
     */
    public function parse($doc = '')
    {
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment
        if (preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false)
            return $this->params;
        $comment = trim($comment [1]);
        // Get all the lines and strip the * from the first character
        if (preg_match_all('#^\s*\*(.*)#m', $comment, $lines) === false)
            return $this->params;
        $this->parseLines($lines [1]);
        return $this->params;
    }

    /**
     *
     * api/action 接口方法文档解析
     * @param string $class_name 类名称
     * @param string $method 方法名称
     * @return bool true
     */
    public static function apiParse($class_name, $api = ''){
        //反射读取对象
        $reflection = new ReflectionClass($class_name);
        //通过反射获取类的注释
        $doc = $reflection->getDocComment();
        //$parase_result = docparse::getInstance(false)->parse($doc);
        //$class_metadata = $parase_result;

        //获取类中的方法，设置获取public,protected类型方法
        if($api) {
            $method = $reflection->getMethod($api . '_Action');
            $methods = array($method);
        }else{
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        }

        $retData = array();
        $data = array();

        //遍历所有的方法
        foreach ($methods as $method) {
            if(!$method->isPublic()) continue;

            $method_name = $method->getName();
            if(stripos($method_name, '_Action') == false) continue;

            //获取方法的注释
            $doc = $method->getDocComment();

            //解析注释
            $info = docparse::getInstance(false)->parse($doc);

            $metadata =  $info;

            //获取方法的参数
            $params = $method->getParameters();
            $position=0;    //记录参数的次序
            $arguments = array();
            $defaults = array();

            foreach ($params as $param){
                $arguments[$param->getName()] = $position;
                //参数是否设置了默认参数，如果设置了，则获取其默认值
                $defaults[$position] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL;
                $position++;
            }

            $data['class_name']     = $class_name;

            $api_name = '/'.str_replace('_Controller', '', $class_name).'/'.str_replace('_Action', '', $method->getName());
            $data['api_name']    = $api_name;//接口名称
            $data['api_description']    = (isset($metadata['long_description'])?$metadata['long_description']:@$metadata['description']);

            $path_url = F::config('domain').substr($api_name, 1);

            $param_arr = array();

            $param_count = count($params);
            if($param_count) {
                foreach ($arguments as $k => $pos) {
                    $param_arr[$pos] = array(
                        'name' => $k,
                        'default' => ($defaults[$pos] ? $defaults[$pos] : "无"),
                        'description' => ($param_count == 1 ? @$metadata['param'] : @$metadata['param'][$pos])
                    );
                    $path_url .= '/'.$k;
                }
            }

            $data['param_count'] = $param_count;
            $data['param_arr'] = $param_arr;

            $data['path_url']  = $path_url;
            $data['return'] = (isset($metadata['return'])?$metadata['return']:'无');

            array_push($retData, $data);

        }

        return $retData;

    }

    /**
     *
     * system/model/controller/other 类方法文档解析
     * @param string $class_name 类名称
     * @param string $method 方法名称
     * @return array 解析结果
     */
    public static function methodParse($class_name, $method = ''){
        $isController = stripos($class_name, '_Controller');
        if($isController){
            return docparse::apiParse($class_name, $method);
        }
        //反射读取对象
        $reflection = new ReflectionClass($class_name);
        //通过反射获取类的注释
        $doc = $reflection->getDocComment();
        //$parase_result = docparse::getInstance(false)->parse($doc);
        //$class_metadata = $parase_result;

        if($method) {
            $method = $reflection->getMethod($method);
            $methods = array($method);
        }else{
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_PROTECTED|ReflectionMethod::IS_PRIVATE);
        }

        $retData = array();
        $data = array();

        //遍历所有的方法
        foreach ($methods as $method) {
            $method_name = $method->getName();
            if(stripos($method_name, '__') !== false) continue;

            //获取方法的注释
            $doc = $method->getDocComment();

            //解析注释
            $metadata = docparse::getInstance(false)->parse($doc);

            //获取方法的参数
            $params = $method->getParameters();
            $position=0;    //记录参数的次序
            $arguments = array();
            $defaults = array();

            foreach ($params as $param){
                $arguments[$param->getName()] = $position;
                //参数是否设置了默认参数，如果设置了，则获取其默认值
                $defaults[$position] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL;
                $position++;
            }

            $data['class_name']     = $class_name;
            $data['method_name']    = $method_name;
            $data['method_description']    = (isset($metadata['long_description'])?$metadata['long_description']:@$metadata['description']);

            $data['file_name']  = str_replace(PROROOT, '', $reflection->getFileName());
            $data['start_line'] = $method->getStartLine();

            $data['method_type'] = $method->isStatic()?'静态方法':($method->isPublic()?'公有方法':($method->isProtected()?'保护方法':'私有方法'));

            $param_arr = array();

            $param_count = count($params);
            if($param_count) {
                foreach ($arguments as $k => $pos) {
                    $param_arr[$pos] = array(
                        'name' => $k,
                        'default' => ($defaults[$pos] ? $defaults[$pos] : "无"),
                        'description' => ($param_count == 1 ? @$metadata['param'] : @$metadata['param'][$pos])
                    );
                }
            }

            $data['param_count'] = $param_count;
            $data['param_arr'] = $param_arr;

            $data['return'] = (isset($metadata['return'])?$metadata['return']:'无');

            array_push($retData, $data);
        }

        return $retData;

    }

    private function parseLines($lines)
    {
        $real_lines = array();
        $current_line = '';
        $last_line = count($lines)-1;

        foreach ($lines as $k=>$line) {

            $line = trim($line);

            if (empty ($line)) {
                //最后一行
                if($last_line == $k){
                    array_push($real_lines, $current_line);
                }
                continue;
            }

            if (strpos($line, '@') === false) {
                if(empty($current_line)) {
                    $current_line = $line;
                }else{
                    $current_line .= '<br />'.$line;
                }

            } elseif(strpos($line, '@') === 0) {

                if(!empty($current_line)){
                    array_push($real_lines, $current_line);
                    $current_line = '';
                }
                $current_line = $line;
            }
            //最后一行
            if($last_line == $k){
                array_push($real_lines, $current_line);
            }
        }

        foreach ($real_lines as $line) {
            $parsedLine = $this->parseLine($line); // Parse the line

            if ($parsedLine === false && !isset ($this->params ['description'])) {
                if (isset ($desc)) {
                    // Store the first line in the short description
                    $this->params ['description'] = implode(PHP_EOL, $desc);
                }
                $desc = array();
            } elseif ($parsedLine !== false) {
                $desc [] = $parsedLine; // Store the line in the long description
            }
        }
        $desc = implode(' ', $desc);
        if (!empty ($desc))
            $this->params ['long_description'] = $desc;
    }

    private function parseLine($line)
    {
        // trim the whitespace from the line
        $line = trim($line);

        if (empty ($line))
            return false; // Empty line

        if (strpos($line, '@') === 0) {
            if (strpos($line, ' ') > 0) {
                // Get the parameter name
                $param = substr($line, 1, strpos($line, ' ') - 1);
                $value = substr($line, strlen($param) + 2); // Get the value
            } else {
                $param = substr($line, 1);
                $value = '';
            }
            // Parse the line and return false if the parameter is valid
            if ($this->setParam($param, $value))
                return false;
        }

        return $line;
    }

    private function setParam($param, $value)
    {
        if ($param == 'param' || $param == 'return')
            $value = $this->formatParamOrReturn($value);
        if ($param == 'class')
            list ($param, $value) = $this->formatClass($value);

        if (empty ($this->params [$param])) {
            $this->params [$param] = $value;
        } else if ($param == 'param') {
            if(is_array($this->params [$param])){
                $this->params [$param][] = $value;
            }else{
                $this->params [$param] = array($this->params [$param], $value);
            }
        } else {
            $this->params [$param] = $value + $this->params [$param];
        }
        return true;
    }

    private function formatClass($value)
    {
        $r = preg_split("[|]", $value);
        if (is_array($r)) {
            $param = $r [0];
            parse_str($r [1], $value);
            foreach ($value as $key => $val) {
                $val = explode(',', $val);
                if (count($val) > 1)
                    $value [$key] = $val;
            }
        } else {
            $param = 'Unknown';
        }
        return array(
            $param,
            $value
        );
    }

    private function formatParamOrReturn($string)
    {
        $pos = strpos($string, ' ');

        $type = substr($string, 0, $pos);
        return '(' . $type . ')' . substr($string, $pos + 1);
    }

}