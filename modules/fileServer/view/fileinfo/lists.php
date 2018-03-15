<?php include View::output('admin/global/header.php'); ?>
<?php include View::output('admin/global/nav_menu.php'); ?>

 <style type="text/css">

  table{table-layout: fixed;}
  table td{
   word-break:break-all;
   word-wrap:break-word;
  }

 </style>

<div class="aw-content-wrap" id="user_list">
 <div class="mod">
  <div class="mod-head">
   <h3>
    <ul class="nav nav-tabs">
     <li class="active"><a href="<?php echo F::config('domain');?>fileServer/fileinfo/lists">文件列表</a></li>
    </ul>
   </h3>
  </div>
  <div class="mod-body tab-content">
   <div class="tab-pane active" id="list">
    <?php if (input::get('action') == 'search') { ?>
     <div class="alert alert-info"><?php _e ('找到 %s 条符合条件的内容', $total_rows); ?></div>
    <?php } ?>

    <div class="table-responsive">
     <?php if ($this->lists) : ?>
      <form method="post" action="admin/ajax/remove_users/" id="users_form">
       <table class="table table-striped">
        <thead>
        <tr>
         <th width="30"><input type="checkbox" class="check-all"></th>
         <th width="50"><?php _e('ID'); ?></th>
         <th width="60"><?php _e('帐号ID'); ?></th>
         <th><?php _e('文件名'); ?></th>
         <th><?php _e('文件类型'); ?></th>
         <th><?php _e('文件大小'); ?></th>
         <th><?php _e('创建时间'); ?></th>
         <th width="200"><?php _e('唯一HASH值'); ?></th>
         <th><?php _e('文件后缀名'); ?></th>
         <th><?php _e('操作'); ?></th>
        </tr>
        </thead>
        <tbody>

         <?php foreach ($lists as $k=>$arr):?>
          <tr>
           <td><input type="checkbox" value="<?php echo $arr['uid']; ?>" name="uids[]"></td>
           <?php foreach ($arr as $k=>$v):?>
            <?php if(in_array($k, $list_fields_arr)) {
             $param = $k.'_arr';
             if (isset($$param) && is_array($$param)){
              echo '<td>'.F::findInArray($v, $$param).'</td>';
             }else{
              if($k == 'add_time') echo '<td>'.date('Y-m-d H:i:s', $v).'</td>';
              else echo '<td>'.$v.'</td>';
             }
            }
            ?><?php endforeach;?>
           <td>
            <a class="icon icon-share md-tip" data-original-title="查看详情" href="fileServer/fileinfo/show/<?php echo $arr['id'];?>"></a>
            <a class="icon icon-edit md-tip" data-original-title="修改" href="fileServer/fileinfo/edit/<?php echo $arr['id'];?>"></a>
            <a class="icon icon-down md-tip" data-original-title="删除" href="fileServer/fileinfo/delete/<?php echo $arr['id'];?>"></a>
           </td>
          </tr>
         <?php endforeach;?>


        </tbody>
        </table>
       </form>
      <?php endif;?>
     </div>
    </div>

    <div class="mod-table-foot">
     <div class="col-sm-4 col-xs-12">
      <a class="btn btn-danger" onclick="AWS.ajax_post($('#users_form'));"><?php _e('删除'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
      <a class="btn btn-success" href="javascript:history.go(-1);">返回</a>
     </div>
     <div class="col-xs-12 col-sm-8">
      <?php echo $this->pagination; ?>
     </div>
    </div>
   </div>


 </div>
</div>

<?php include View::output('admin/global/footer.php'); ?>