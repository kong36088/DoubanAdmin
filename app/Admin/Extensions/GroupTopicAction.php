<?php

namespace App\Admin\Extensions;

use App\Group;
use Encore\Admin\Admin;

class GroupTopicAction
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.dislike-group-topic').on('click', function () {
    if(confirm('是否要忽略该条信息？')){
        // Your code.
        var url = encodeURIComponent($(this).data('url'));
        
        $.get('/douban/dislike?url='+url,function(result){
            if(result == '1'){
                toastr.success('操作成功！');
                $.pjax.reload('#pjax-container');
            }else{
                toastr.error('操作失败');
            }
        });
    }
    
});

$('.star-group-topic').on('click', function () {
    // Your code.
    var url = encodeURIComponent($(this).data('url'));    
    
    $.get('/douban/star?star=1&url='+url,function(result){
        if(result == '1'){
            //alert('标记成功！');
            toastr.success('操作成功！');
            $.pjax.reload('#pjax-container');
        }else{
            toastr.error('操作失败');
        }

    });
    
});

$('.unstar-group-topic').on('click', function () {
    // Your code.
    var url = encodeURIComponent($(this).data('url'));    
    
    $.get('/douban/star?star=0&url='+url,function(result){
        if(result == '1'){
            //alert('标记成功！');
            toastr.success('操作成功！');
            $.pjax.reload('#pjax-container');
        }else{
            toastr.error('操作失败');
        }

    });
    
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        $star = Group::where('url', $this->id)->first()->star;
        if ($star) {
            $content = '<a class="unstar-group-topic" href="javascript:void(0);" data-url="' . $this->id . '"><i class="fa fa-star-o"></i>取消标记</a>';
        } else {
            $content = '<a class="star-group-topic" href="javascript:void(0);" data-url="' . $this->id . '"><i class="fa fa-star"></i>标为喜欢</a>';
        }
        $content .= '<a class="dislike-group-topic" href="javascript:void(0);" data-url="' . $this->id . '"><i class="fa fa-close"></i>不再显示</a>';
        return $content;
    }

    public function __toString()
    {
        return $this->render();
    }
}