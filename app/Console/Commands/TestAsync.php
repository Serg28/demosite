<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Spatie\Async\Pool;
use VXM\Async\AsyncFacade as Async;

class TestAsync extends Command
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'async:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pcntlLoaded = extension_loaded('pcntl');
        $posixLoaded = extension_loaded('posix');
        echo Pool::isSupported();
        if ($pcntlLoaded) {
            echo "Расширение pcntl установлено.\n";
        } else {
            echo "Расширение pcntl не установлено.\n";
        }

        if ($posixLoaded) {
            echo "Расширение posix установлено.\n";
        } else {
            echo "Расширение posix не установлено.\n";
        }

        $pool = Pool::create();
        $startTime = microtime(true);
        for ($i = 1; $i < 200; $i++) {
            /*Async::run(function () use ($i, $out) {
                sleep(1);
                return $i;
            });*/


            $pool->add(function () use ($i) {
                sleep(1);
            })->then(function ($output) {
                $this->output->write('.');
            });
        }
        $pool->wait();
        //Async::wait();
        $this->output->write(microtime(true) - $startTime);
        //var_dump(implode(', ', Async::wait()));
    }
}
