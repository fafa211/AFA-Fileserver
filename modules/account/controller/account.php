<?php defined('AFA') or die('No AFA PHP Framework!');

/**
 * account模型控制器
 * @author zhengshufa
 * @date 2016-08-26 16:29:46
 */
class account_Controller extends Admin_Controller
{

    private $vdir = '';

    /**
     * 新增account
     */
    public function add_Action()
    {
        if (input::post()) {
            $post = input::post();
            $account = new account_Model();
            foreach ($post as $k => $v) {
                $account->$k = is_array($v) ? join(',', $v) : $v;
            }
            $account->regtime = date('Y-m-d H:h:s');

            $account->save();
            $account->passwd = md5($account->id.','.$account->account);
            $account->keyno = F::authstr($account->id.','.$account->account, 'ENCODE');
            $account->save();


            $this->echomsg('新增成功!', 'lists');
        }
        $view = &$this->view;
        $view->set_view($this->vdir . 'add');
        $view->render();
    }

    /**
     * 删除account
     */
    public function delete_Action($id)
    {
        $account = new account_Model($id);
        if ($account->id) {
            $account->delete($id);
            $this->echomsg('删除成功!', '../lists');
        } else {
            $this->echomsg('删除失败!', '../lists');
        }
    }

    /**
     * 修改account
     */
    public function edit_Action($id)
    {
        $account = new account_Model($id);
        if (input::post()) {
            $post = input::post();
            foreach ($post as $k => $v) {
                $account->$k = is_array($v) ? join(',', $v) : $v;
            }
            $account->save();
            $this->echomsg('修改成功!', '../lists');
        }
        $view = &$this->view;
        $view->set_view($this->vdir . 'edit');
        $view->account = $account;
        $view->render();
    }

    /**
     * 列表管理 account
     */
    public function lists_Action()
    {
        $account = new account_Model();
        $view = &$this->view;
        $view->set_view($this->vdir . 'lists');
        $view->lists = $account->lists('0,10');
        $view->list_fields_arr = array('id', 'account', 'passwd', 'keyno', 'bindip', 'loginip', 'logintime', 'regtime');
        $view->render();
    }

    /**
     * 展示account
     */
    public function show_Action($id)
    {
        $account = new account_Model($id);
        $view = &$this->view;
        $view->set_view($this->vdir . 'show');
        $view->account = $account;
        $view->render();
    }


    public function test_Action(){
        $key = '92ff8AwUDBQhTCVRRCQEAUVRQAlUDCFUGBFteWFwGHxhbAVhURltAXgI';
        $rtstr = F::authstr($key, 'DECODE');
        echo $rtstr;





    }

}
