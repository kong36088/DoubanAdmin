<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\GroupTopicAction;
use App\Group;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GroupController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('我的收藏');
            $content->description('收藏的帖子');

            $content->body($this->grid());
        });
    }

    public function showDetail(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $url = $request->input('url');

            /** @var Collection $info */
            $info = Group::where('url', $url)->first();

            $content->header('帖子内容');
            if(empty($info)){
                return;
            }

            $content->description('正在查看豆瓣帖子，标题：' . $info->title);



            $content->body(view('GroupTopicDetail', ['info'=>$info]));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Group::class, function (Grid $grid) {
            $grid->model()->where('dislike', '!=', 1)->orderBy('last_reply_time', 'desc');

            $grid->column('url','#')->display(function ($url) {
                $url = urlencode($url);
                return "<a href=/douban/detail?url={$url}><i class='fa fa-desktop'></i>查看</a>";
            });


            $grid->column('title', '标题');

            $grid->last_reply_time('最后回复时间')->sortable();
            $grid->reply_num('回复数量')->sortable();
            $grid->create_time('抓取时间')->sortable();
            $grid->group_id('小组名称')->sortable();
            $grid->author('发帖人');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                // append一个操作
                $actions->append(new GroupTopicAction($actions->getKey()));

                // prepend一个操作
            });

            $grid->filter(function ($filter) {
                //$filter->like('URL', '模糊匹配url');
                //$filter->disableIdFilter();

                // 在这里添加字段过滤器

                $filter->like('title', '标题关键词1');


                $filter->where(function ($query) {
                    $query->where('title', 'like', "%{$this->input}%");
                }, '标题关键词2');

            });

            $grid->disableExport();

            $grid->disableCreation();
            $grid->disableRowSelector();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Group::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function dislike(Request $request)
    {
        $url = $request->input('url');
        if (empty($url)) {
            return;
        }
        Group::where('url', $url)->update(['dislike' => 1]);
        return response('1');
    }

    protected function star(Request $request)
    {
        $url = $request->input('url');
        $star = $request->input('star', null);
        if (empty($url) || $star === null) {
            return;
        }
        Group::where('url', $url)->update(['star' => $star]);
        return response('1');
    }
}
