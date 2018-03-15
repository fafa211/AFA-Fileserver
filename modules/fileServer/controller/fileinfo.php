<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * fileinfo模型控制器
 * @author zhengshufa
 * @date 2016-09-01 10:16:52
 */
class fileinfo_Controller extends Admin_Controller
{

    private $vdir = 'fileinfo/';

    /**
     * 新增fileinfo
     */
    public function add_Action()
    {
        if (input::post()) {
            $post = input::post();
            $fileinfo = new fileinfo_Model();
            foreach ($post as $k => $v) {
                $fileinfo->$k = is_array($v) ? join(',', $v) : $v;
            }
            $fileinfo->save();
            $this->echomsg('新增成功!', 'lists');
        }
        $view = &$this->view;
        $view->set_view($this->vdir . 'add');
        $view->render();
    }

    /**
     * 删除fileinfo
     */
    public function delete_Action($id)
    {
        $fileinfo = new fileinfo_Model($id);
        if ($fileinfo->id) {
            $fileinfo->delete($id);
            $this->echomsg('删除成功!', '../lists');
        } else {
            $this->echomsg('删除失败!', '../lists');
        }
    }

    /**
     * 修改fileinfo
     */
    public function edit_Action($id)
    {
        $fileinfo = new fileinfo_Model($id);
        if (input::post()) {
            $post = input::post();
            foreach ($post as $k => $v) {
                $fileinfo->$k = is_array($v) ? join(',', $v) : $v;
            }
            $fileinfo->save();
            $this->echomsg('修改成功!', '../lists');
        }
        $view = &$this->view;
        $view->set_view($this->vdir . 'edit');
        $view->fileinfo = $fileinfo;
        $view->render();
    }

    /**
     * 列表管理 fileinfo
     */
    public function lists_Action()
    {
        $fileinfo = new fileinfo_Model();
        $view = &$this->view;
        $view->set_view($this->vdir . 'lists');

        $total = $fileinfo->count();
        $pagination = new Page($total, 20);

        $view->lists = $fileinfo->lists($pagination->limit);

        $view->pagination = $pagination;

        $view->list_fields_arr = array('id', 'account_id', 'file_name', 'file_type', 'file_size', 'add_time', 'hash_id', 'suffix');
        $view->render();
    }

    /**
     * 展示fileinfo
     */
    public function show_Action($id)
    {
        $fileinfo = new fileinfo_Model($id);
        $view = &$this->view;
        $view->set_view($this->vdir . 'show');
        $view->fileinfo = $fileinfo;
        $view->render();
    }

}
