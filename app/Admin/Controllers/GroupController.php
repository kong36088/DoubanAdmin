<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\GroupTopicAction;
use App\Group;

use App\GroupMark;
use App\SearchRecord;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $url = urldecode($request->input('url'));

            /** @var Collection $info */
            $info = Group::where('url', $url)->first();
            $star = GroupMark::where(['url' => $url, 'user_id' => Admin::user()->id, 'type' => 'star'])->pluck('value')->toArray();
            $dislike = GroupMark::where(['url' => $url, 'user_id' => Admin::user()->id, 'type' => 'dislike'])->pluck('value')->toArray();
            $status['star'] = empty($star) || $star[0] == 0 ? 0 : 1;
            $status['dislike'] = empty($dislike) || $dislike[0] == 0 ? 0 : 1;

            $content->header('帖子内容');
            if (empty($info)) {
                return;
            }

            $content->description('正在查看豆瓣帖子，标题：' . $info->title);


            $content->body(view('GroupTopicDetail', ['info' => $info, 'status' => $status]));
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
            $notIN = GroupMark::where(['user_id' => $userId, 'type' => 'dislike', 'value' => 1])->pluck('url')->toArray();
            $grid->model()->whereNotIn('url', $notIN)->orderBy('last_reply_time', 'desc');

            $grid->column('url', '#')->display(function ($url) use ($userId) {
                $url = urldecode($url);
                $urlen = urlencode($url);
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


            $grid->column('title', '标题')->display(function ($title) use ($userId) {
                $url = urldecode($this->url);
                $urlen = urlencode($url);
                $type = 'read';
                if (GroupMark::where(['url' => $url, 'user_id' => $userId, 'type' => $type])->get()->isEmpty()) {
                    return "<a data-url='{$urlen}' href='javascript:void(0);'
                            class='group-topic-read-detail'>
                            {$title}</a>";
                } else {
                    return "<a data-url='{$urlen}' href='javascript:void(0);'
                            class='group-topic-read-detail' style='color:dimgrey'>
                            {$title}</a>";
                }
            });

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

            $grid->filter(function ($filter) use ($userId) {
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
                    $query->where('title', 'not like', "%{$_GET['not_title1']}%");
                }, '反选关键字1', 'not_title1');
                $filter->where(function ($query) {
                    $query->where('title', 'not like', "%{$_GET['not_title2']}%");
                }, '反选关键字2', 'not_title2');

                $filter->equal('group_id', '小组_id');

                $filter->where(function ($query) {
                    $query->where('author',trim($this->input));
                }, 'author', '发帖人');

                //搜索记录
                $recordFields = ['primary' => ['title'], 'secondary' => ['title_1', 'title_2', 'title_3'], 'not' => ['not_title1', 'not_title2']];
                $search = [];
                $searchValue = '';
                foreach ($recordFields as $k => $f) {
                    foreach ($f as $v) {
                        if (!empty($_GET[$v])) {
                            if (empty($search[$k])) {
                                $search[$k] = '';
                            }
                            $search[$k] .= "{$_GET[$v]},";
                            $searchValue .= "{$v}={$_GET[$v]}&";
                        }
                    }
                    if (isset($search[$k])) {
                        $search[$k] = rtrim($search[$k], ',');
                    }
                }
                $searchValue = rtrim($searchValue, '&');
                $search['user_id'] = $userId;
                $search['value'] = $searchValue;
                if (!empty($searchValue)) {
                    SearchRecord::insert($search);
                }

            });

            $grid->disableExport();

            $grid->disableCreation();
            $grid->disableRowSelector();

            //顶部工具
            $grid->tools(function ($tools) use ($userId) {
                /** @var Collection $records 查询top3搜索历史 */
                $records = SearchRecord::where('user_id', $userId)->select(DB::raw('count(1) as num,user_id,value'))
                    ->groupBy('user_id', 'value')->orderBy('num', 'desc')->limit(3)->get();
                $top3 = array();
                foreach ($records as $r) {
                    $top3[] = SearchRecord::where('user_id', $r->user_id)->where('value', $r->value)
                        ->first()->toArray();
                }
                $tools->append(new \App\Admin\Extensions\Tools\SearchRecord($top3));
            });

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
        $url = urldecode($request->input('url'));
        if (empty($url)) {
            return;
        }
        Group::where('url', $url)->update(['dislike' => 1]);
        return response('1');
    }

    protected function star(Request $request)
    {
        $url = urldecode($request->input('url'));
        $star = $request->input('star', null);
        if (empty($url) || $star === null) {
            return;
        }
        Group::where('url', $url)->update(['star' => $star]);
        return response('1');
    }

    protected function mark(Request $request)
    {
        $url = urldecode($request->input('url'));
        $userId = Admin::user()->id;
        $type = $request->input('type');
        $value = $request->input('value', null);
        if (empty($url) || $value === null || empty($type)) {
            return;
        }
        if (GroupMark::where(['url' => $url, 'user_id' => $userId, 'type' => $type])->get()->isEmpty()) {
            GroupMark::insert(['url' => $url, 'user_id' => $userId, 'type' => $type, 'value' => $value]);
        } else {
            GroupMark::where(['url' => $url, 'user_id' => $userId, 'type' => $type])->update(['value' => $value]);
        }
        return response('1');
    }
}
