<div class="records pull-right col-sm-10" style="display:inline-block;">
@if (!empty($records))
    <h5 style="">我的最近的搜索TOP3：</h5>
        @foreach($records as  $record)
            <div class="record-block" style="display:block;line-height:25px;">
                <a href="/douban?{{$record['value']}}" style="text-decoration: none;color:black;" class=>
                    <p class="label label-danger">主：{{$record['primary']}}</p>
                    <p class="label label-info">次：{{$record['secondary']}}</p>
                    <p class="label label-default">非：{{$record['not']}}</p>
                </a>
            </div>
        @endforeach
    @endif
</div>