<div style="margin-bottom: 10px;">
    <a href="{{urldecode($info->url)}}" target="_blank">
        <button class="btn btn-sm btn-primary grid-refresh">
            <i class="fa fa-refresh"></i> 查看原帖
        </button>
    </a>
    <button class="btn btn-sm btn-primary grid-refresh" onclick="refresh()">
        <i class="fa fa-refresh"></i> 刷新
    </button>
    <button class="btn btn-sm btn-primary grid-refresh" onclick="back()">
        <i class="fa fa-backward"></i> 返回
    </button>
    @if ($status['star'] != 1)
        <a class="star-group-topic" href="javascript:void(0);" data-url="{{$info->url}}"><i class="fa fa-star"></i>标为喜欢</a>
    @else
        <a class="unstar-group-topic" href="javascript:void(0);" data-url="{{$info->url}}"><i class="fa fa-star-o"></i>取消标记</a>
    @endif
    <a class="dislike-group-topic" href="javascript:void(0);" data-url="{{$info->url}}"><i class="fa fa-close"></i>不再显示</a>
</div>

<iframe src="{{$info->url}}" width="100%" height="850px" frameborder="0"
        security="restricted" sandbox=""></iframe>

<script type="application/javascript">
    function refresh() {
        $.pjax.reload('#pjax-container');
    }

    function back() {
        history.back();
    }

    $('.dislike-group-topic').on('click', function () {
        if (confirm('是否要忽略该条信息？')) {
            // Your code.
            var url = encodeURIComponent($(this).data('url'));

            $.get('/douban/mark?value=1&type=dislike&url=' + url, function (result) {
                if (result == '1') {
                    toastr.success('操作成功！');
                    history.back();
                } else {
                    toastr.error('操作失败');
                }
            });
        }

    });

    $('.star-group-topic').on('click', function () {
        // Your code.
        var url = encodeURIComponent($(this).data('url'));

        $.get('/douban/mark?value=1&type=star&url=' + url, function (result) {
            if (result == '1') {
                //alert('标记成功！');
                toastr.success('操作成功！');
                $.pjax.reload('#pjax-container');
            } else {
                toastr.error('操作失败');
            }

        });

    });

    $('.unstar-group-topic').on('click', function () {
        // Your code.
        var url = encodeURIComponent($(this).data('url'));

        $.get('/douban/mark?value=0&type=star&url=' + url, function (result) {
            if (result == '1') {
                //alert('标记成功！');
                toastr.success('操作成功！');
                $.pjax.reload('#pjax-container');
            } else {
                toastr.error('操作失败');
            }

        });

    });

</script>