<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\GroupTopicAction;
use App\Group;

use App\GroupMark;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class GroupStarController extends Controller
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
            $userId = Admin::user()->id;
            $in = GroupMark::where(['user_id'=>$userId,'type'=>'star','value'=>1])->pluck('url')->toArray();

            $grid->model()->whereIn('url', $in)->orderBy('last_reply_time', 'desc');

            $grid->column('url', '#')->display(function ($url) {
                $url = urldecode($url);
                $urlen = urlencode($url);
                $userId = Admin::user()->id;
                $type = 'read';
                if (GroupMark::where(['url' => $url, 'user_id' => $userId, 'type' => $type])->get()->isEmpty()) {
                    return "<a data-url='{$urlen}' href='javascript:void(0);'
                            class='group-topic-read-detail'>
                            <i class='fa fa-desktop'></i>查看</a>";
                } else {
                    return "<a data-url='{$urlen}' href='javascript:void(0);'
                            class='group-topic-read-detail' style='color:dimgrey'>
                            <i class='fa fa-desktop'></i>查看</a>";
                }
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
                $actions->append(new GroupTopicAction($actions->getKey(), Admin::user()->id));

                // prepend一个操作
            });

            $grid->filter(function ($filter) {
                //$filter->disableIdFilter();

                $filter->where(function ($query) {
                    if (empty($_GET['title_1']) && empty($_GET['title_2']) && empty($_GET['title_3'])) {
                        $query->where('title', 'like', "%{$this->input}%");
                    } else {
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
                $filter->where(function ($query) {
                    $query->where('title','not like',"%{$_GET['not_title1']}%");

                }, '反选关键字1', 'not_title1');
                $filter->where(function ($query) {
                    $query->where('title','not like',"%{$_GET['not_title2']}%");

                }, '反选关键字2', 'not_title2');

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
}
