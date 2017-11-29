<div style="margin-bottom: 10px;">
    <a class="btn btn-sm btn-primary grid-refresh" onclick="refresh()">
        <i class="fa fa-refresh"></i> 刷新
    </a>
    <a class="btn btn-sm btn-primary grid-refresh" onclick="history.back()">
        <i class="fa fa-backward"></i> 返回
    </a>
</div>

<iframe src="{{$info->url}}" width="100%" height="850px" frameborder="0"
        security="restricted" sandbox=""></iframe>

<script type="application/javascript">
    function refresh() {
        $.pjax.reload('#pjax-container');
    }

</script>