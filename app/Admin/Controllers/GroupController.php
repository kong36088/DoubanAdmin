<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DislikeGroupTopic;
use App\Group;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

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

            $content->header('豆瓣租房信息');
            $content->description('定时更新');

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
            $grid->model()->where('dislike', '!=', 1);

            $grid->column('#')->display(function () {
                return "";
            });

            $grid->column('title', '标题');

            $grid->update_time('最后回复时间')->sortable();
            $grid->reply_num('回复数量')->sortable();
            $grid->create_time('抓取时间')->sortable();
            $grid->group_id('小组名称')->sortable();
            $grid->author('发帖人');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                // append一个操作
                $actions->append(new DislikeGroupTopic($actions->getKey()));

                // prepend一个操作
            });

            $grid->filter(function($filter){
                //$filter->like('URL', '模糊匹配url');

                // 在这里添加字段过滤器

                $filter->like('title', '标题关键词1');


                $filter->where(function ($query) {
                    $query->where('title', 'like', "%{$this->input}%");
                }, '标题关键词2');

            });

            $grid->disableExport();

            $grid->perPages([30, 40, 50]);
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
        if(empty($url)){
            return;
        }
        Group::where('url', $url)->update(['dislike' => 1]);
    }
}
