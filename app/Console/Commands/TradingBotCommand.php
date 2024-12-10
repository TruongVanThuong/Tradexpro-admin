<?php

namespace App\Console\Commands;

use App\Http\Services\TradingBotService;
use App\Jobs\BotOrderJob;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TradingBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading:bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Default trading bot that place buy and sell order ';

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
        storeBotException('TradingBotCommand running', date('Y-m-d H:i:s'));

            while(true) {
                $sleep = 0;
                $start = Carbon::now();
                if(allsetting('enable_bot_trade') == STATUS_ACTIVE) {
                    $service = new TradingBotService();

                    // Execute your command here
                    storeBotException('TradingBot  start', date('Y-m-d H:i:s'));
                    $response = $service->placeBotOrder(1);
                    storeBotException('TradingBot  end', date('Y-m-d H:i:s'));
                    $end = Carbon::now();
                    $differenceInSeconds = $end->diffInSeconds($start);
                    storeBotException('TradingBotCommand differenceInSeconds', $differenceInSeconds);
                    $intervalInSec = intval(settings('trading_bot_buy_interval') ?? 5);
                    $sleep = 0;
                    if ($differenceInSeconds < $intervalInSec) {
                        $sleep = $intervalInSec - $differenceInSeconds;
                    }
                    storeBotException('TradingBotCommand sleep => ', $sleep);
                    sleep($sleep);
                }
            }
            storeBotException('TradingBotCommand end', date('Y-m-d H:i:s'));
    }
}
