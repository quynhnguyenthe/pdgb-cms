<?php

namespace App\Console\Commands;

use App\Models\Matches;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $dateTime = date('Y-m-d H:i:s');
        $matches = DB::table('matches')
            ->where('match_date', '<=', $date)
            ->where('match_time', '<=',$time)
            ->where('status', Matches::STATUS_ACCEPTED)
            ->update(['status' => Matches::STATUS_IN_DUE]);
        DB::table('matches')
            ->where('match_end_date', '<=', $dateTime)
            ->where('status', Matches::STATUS_IN_DUE)
            ->update(['status' => Matches::WAIT_RESULT]);
        echo "\033[01;33mSuccess\033[0m" . PHP_EOL;;
    }
}
