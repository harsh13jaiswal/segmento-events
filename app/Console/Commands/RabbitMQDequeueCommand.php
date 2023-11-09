<?php

namespace App\Console\Commands;

use App\Services\EventLogsService;
use Illuminate\Console\Command;

class RabbitMQDequeueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:dequeue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lib = new EventLogsService();
        $lib->dequeue('event_logs_queue');
    }
}
