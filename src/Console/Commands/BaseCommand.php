<?php

namespace CapeAndBay\ZingFit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

abstract class BaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public $start;
    public $cron_name = 'Base Command';
    public $cron_log = 'base-command-log';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->start = microtime(true);
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Now running {$this->cron_name} \n");

        $this->start();

        $this->info("fin.\n");
        $this->trackTheTime($this->start);
    }

    public function start()
    {
        // This should be handled by the child. Just here for shits and giggles.
        $this->info('Derp');
    }

    public function logTheDate(array $props)
    {
        $good_date = $this->getTheGoodDate();

        //Log the activity
        $new_props = array_merge($props, [
            'time_executed' => $good_date,
        ]);
        /*
        activity($this->cron_log)
            ->withProperties($new_props)
            ->log("{$this->cron_name} executed.");
        */
    }

    public function trackTheTime($start)
    {
        $time_elapsed_secs = microtime(true) - $start;
        $duration = $time_elapsed_secs;
        $hours = (int)($duration/60/60);
        $minutes = (int)($duration/60)-$hours*60;
        $seconds = (int)$duration-$hours*60*60-$minutes*60;

        $finished_at = $this->getTheGoodDate();
        Log::alert("Time to finish - {$hours}:{$minutes}:{$seconds} on {$finished_at}");

        $this->info('Time to finish - '."{$hours}:{$minutes}:{$seconds} on {$finished_at}\n");
    }

    public function getTheGoodDate()
    {
        $from='UTC';
        // @todo - add timezone to users table, default America/NewYork, but central for trufit.
        $to='America/New_York';
        $format='Y-m-d H:i:s';
        $date = date('Y-m-d g:i:s');// UTC time
        date_default_timezone_set($from);
        $newDatetime = strtotime($date);
        date_default_timezone_set($to);
        $time_created = date($format, $newDatetime);
        date_default_timezone_set('UTC');

        return date('m-d-Y @ g:ia', strtotime($time_created.' - 12 HOUR'));
    }

    function progress_bar($done, $total, $info="", $width=50) {
        $perc = round(($done * 100) / $total);
        $bar = round(($width * $perc) / 100);
        $this->info(sprintf("%s%%[%s>%s]%s\r", $perc, str_repeat("=", $bar), str_repeat(" ", $width-$bar), $info));
        system('clear');

    }
}
