<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * fileinfo模型
 * @author zhengshufa
 * @date 2016-09-01 10:16:52
 */
class fileinfo_Model extends Model
{

    protected $module = 'fileServer';
    protected $table = 'file_infos';
    protected $primary = 'id';
    protected $fileds = array(
        'id'            => 0,
        'account_id'    => '0',
        'file_name'     => '',
        'file_type'     => '',
        'file_size'     => '0',
        'add_time'      => '0',
        'hash_id'       => '',
        'suffix'        => '',
        'is_encrpt'     => false,
        'extend_text'   => ''
    );

    /**
     * @param $hash_id  读取文件信息
     */
    public function getByHashId($hash_id){
        $sql = sql::select('*', $this->table, array('hash_id'=>$hash_id));
        $orm = $this->db->getOneResult($sql);
        if ($orm){
            foreach ($orm as $k => $v) {
                $this->$k = $v;
            }
        }
        return $this;
    }
}
