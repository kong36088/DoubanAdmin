<?php

namespace App\Console\Commands;

use App\SearchRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanSearchRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'douban:clean-search-record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理10天前的搜索记录数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $result = SearchRecord::where("create_time", '<=', date("Y-m-d H:i:s", strtotime("-10 days")))->delete();
        if ($result || $result === 0) {
            $msg = date("Y-m-d H:i:s") . " Clean search record finished";
            echo $msg . PHP_EOL;
            Log::info($msg);
            return true;
        } else {
            $msg = date("Y-m-d H:i:s") . " Clean search record failed";
            echo $msg . PHP_EOL;
            Log::info($msg);
            return false;
        }
    }
}
