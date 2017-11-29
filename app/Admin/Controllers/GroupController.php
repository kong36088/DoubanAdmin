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

            $content->header('豆瓣帖子');
            $content->description('租房神器');

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
            if (empty($info)) {
                return;
            }

            $content->description('正在查看豆瓣帖子，标题：' . $info->title);


            $content->body(view('GroupTopicDetail', ['info' => $info]));
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

            $grid->column('url', '#')->display(function ($url) {
                $url = urlencode($url);
                //TODO 已查看标记 标记分离
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

                $filter->where(function ($query) {
                    if(empty($_GET['title_1'])&&empty($_GET['title_2'])&&empty($_GET['title_3'])){
                        $query->whereRaw('title','like',"%{$this->input}%");
                    }else {
                        if (!empty($_GET['title_1'])) {
                            $query->orWhereRaw("(title like '%{$this->input}%' and title like '%{$_GET['title_1']}%')");
                        }
                        if (!empty($_GET['title_2'])) {
                            $query->orWhereRaw("(title like '%{$this->input}%' and title like '%{$_GET['title_2']}%')");
                        }
                        if (!empty($_GET['title_3'])) {
                            $query->orWhereRaw("(title like '%{$this->input}%' and title like '%{$_GET['title_3']}%')");
                        }
                    }

                }, '标题主关键词', 'title');


                $filter->where(function ($query) {
                }, '标题副关键字1', 'title_1');

                $filter->where(function ($query) {
                }, '标题副关键字2', 'title_2');
                $filter->where(function ($query) {
                }, '标题副关键字3', 'title_3');

                $filter->equal('group_id', '小组_id');

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
