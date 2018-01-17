<?php

namespace App\Admin\Extensions;

use App\Group;
use App\GroupMark;
use Encore\Admin\Admin;

class GroupTopicAction
{
    protected $url;
    protected $userId;

    public function __construct($url,$userId)
    {
        $this->url = $url;
        $this->userId = $userId;
    }

    protected function script()
    {
        return <<<SCRIPT

        
        
$('.dislike-group-topic').off("click").on('click', function () {
    if(confirm('是否要忽略该条信息？')){
        // Your code.
        var url = encodeURIComponent($(this).data('url'));
        
        $.get('/douban/mark?value=1&type=dislike&url='+url,function(result){
            if(result == '1'){
                toastr.success('操作成功！');
                $.pjax.reload('#pjax-container');
            }else{
                toastr.error('操作失败');
            }
        });
    }
    
});

$('.star-group-topic').off("click").on('click', function () {
    // Your code.
    var url = encodeURIComponent($(this).data('url'));    
    
    $.get('/douban/mark?value=1&type=star&url='+url,function(result){
        if(result == '1'){
            //alert('标记成功！');
            toastr.success('操作成功！');
            $.pjax.reload('#pjax-container');
        }else{
            toastr.error('操作失败');
        }

    });
    
});

$('.unstar-group-topic').off("click").on('click', function () {
    // Your code.
    var url = encodeURIComponent($(this).data('url'));    
    
    $.get('/douban/mark?value=0&type=star&url='+url,function(result){
        if(result == '1'){
            //alert('标记成功！');
            toastr.success('操作成功！');
            $.pjax.reload('#pjax-container');
        }else{
            toastr.error('操作失败');
        }

    });
    
});

$('.group-topic-read-detail').off("click").on('click', function () {
    // Your code.
    var url = encodeURIComponent($(this).data('url'));    
    
    $.get('/douban/mark?value=1&type=read&url='+url,function(result){
        if(result == '1'){
            //alert('标记成功！');
        }else{
        }
    });
    
    $.pjax({url:'/douban/detail?url='+url,container: '#pjax-container'})
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());


        $star = GroupMark::where(['url' => $this->url, 'user_id' => $this->userId, 'type' => 'star'])->first();
        $star = empty($star->value) ? 0 : 1;
        if ($star) {
            $content = '<a class="unstar-group-topic" href="javascript:void(0);" data-url="' . $this->url . '"><i class="fa fa-star-o"></i>取消标记</a>';
        } else {
            $content = '<a class="star-group-topic" href="javascript:void(0);" data-url="' . $this->url . '"><i class="fa fa-star"></i>标为喜欢</a>';
        }
        $content .= '<a class="dislike-group-topic" href="javascript:void(0);" data-url="' . $this->url . '"><i class="fa fa-close"></i>不再显示</a>';
        return $content;
    }

    public function __toString()
    {
        return $this->render();
    }
}