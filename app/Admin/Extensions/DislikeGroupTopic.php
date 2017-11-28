<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class DislikeGroupTopic
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
        
        $.get('/admin/douban/dislike?url='+url,function(result){
            $.pjax.reload('#pjax-container');
        });
    }
    
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return '<a class="star-group-topic" href="javascript:void(0);" data-url="' . $this->id . '"><i class="fa fa-star"></i>标为喜欢</a>
                <a class="dislike-group-topic" href="javascript:void(0);" data-url="' . $this->id . '"><i class="fa fa-close"></i>不再显示</a>';
    }

    public function __toString()
    {
        return $this->render();
    }
}