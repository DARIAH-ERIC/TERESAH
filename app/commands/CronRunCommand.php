<?php

# Cron job command for Laravel 4.2
# Inspired by Laravel 5's new upcoming scheduler (https://laravel-news.com/2014/11/laravel-5-scheduler)
#
# Author: Soren Schwert (GitHub: sisou)
#
# Requirements:
# =============
# PHP 5.4
# Laravel 4.2 ? (not tested with 4.1 or below)
# A desire to put all application logic into version control
#
# Installation:
# =============
# 1. Put this file into your app/commands/ directory and name it 'CronRunCommand.php'.
# 2. In your artisan.php file (found in app/start/), put this line: 'Artisan::add(new CronRunCommand);'.
# 3. On the server's command line, run 'php artisan cron:run'. If you see a message telling you the
#    execution time, it works!
# 4. On your server, configure a cron job to call 'php-cli artisan cron:run >/dev/null 2>&1' and to
#    run every five minutes (*/5 * * * *)
# 5. Observe your laravel.log file (found in app/storage/logs/) for messages starting with 'Cron'.
#
# Usage:
# ======
# 1. Have a look at the example provided in the fire() function.
# 2. Have a look at the available schedules below (starting at line 132).
# 4. Code your schedule inside the fire() function.
# 3. Done. Now go push your cron logic into version control!

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Services\DataServiceInterface as DataService;
use Services\DataTypeServiceInterface as DataTypeService;
use Services\HarvesterServiceInterface as HarvesterService;
use Services\ToolServiceInterface as ToolService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CronRunCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cron:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduler';

    /**
     * Current timestamp when command is called.
     *
     * @var integer
     */
    protected $timestamp;

    /**
     * Hold messages that get logged
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Specify the time of day that daily tasks get run
     *
     * @var string [HH:MM]
     */
    protected $runAt = '03:00';

    /**
     * The schema.org main SoftwareApplication string
     *
     * @var string
     */
    private static $vocabSoftwareApplication = "http://schema.org/SoftwareApplication";

    protected $toolService;
    protected $dataService;
    protected $dataTypeService;
    protected $harvesterService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ToolService $toolService, DataService $dataService, DataTypeService $dataTypeService, HarvesterService $harvesterService)
    {
        parent::__construct();

        $this->toolService = $toolService;
        $this->dataService = $dataService;
        $this->dataTypeService = $dataTypeService;
        $this->harvesterService = $harvesterService;
        $this->timestamp = time();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //Use weekly for all sources
        $this->weekly(function() {
            $allHarvesters = $this->harvesterService->all();
            Log::info("Starting all harvesters: " . sizeof($allHarvesters));
            foreach($allHarvesters as $harvester) {
                $this->harvest($harvester);
            }
        });

        //Use every 5 minutes for all sources that are in an urgent state
        $this->everyFiveMinutes(function()
        {
            $allUrgent = $this->harvesterService->findAllUrgent();
            Log::info("Starting all urgent: " . sizeof($allUrgent));
            foreach($allUrgent as $harvester) {
                $this->harvest($harvester);
            }
        });

        $this->finish();
    }

    protected function finish()
    {
        // Write execution time and messages to the log
        $executionTime = round(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000), 3);
        Log::info('Cron: execution time: ' . $executionTime . ' | ' . implode(', ', $this->messages));
    }

    protected function harvest($harvester)
    {
        Log::info("Harvesting the url: " . $harvester->url);
        $dataTypes = $this->dataTypeService->all()->get();
        $sourceId = $harvester->data_source_id;

        $client = new Client();
        $crawler = $client->request('GET', urldecode($harvester->url));
        $crawler->filter('*[vocab="http://schema.org/"][typeof="SoftwareApplication"]')->each(function (Crawler $node) use($dataTypes, $sourceId, &$tools, &$toolsFullyDescribed) {
            $vocab = $node->attr("vocab");
            $typeof = $node->attr("typeof");
            if(($vocab.$typeof) == static::$vocabSoftwareApplication) {
                $selectedTool = $node->filter("*[property='name']")->each(function (Crawler $subNode) {
                    $toolFound = $this->toolService->findByNameAndTrashed($subNode->text());
                    if ($toolFound) {
                        if ($toolFound->trashed()) {
                            $toolFound->restore();
                        }
                    } else {
                        Tool::create($this->inputWithAuthenticatedUserId(array("name" => $subNode->text(), "is_filled" => false)));
                        $toolFound = $this->toolService->findByName($subNode->text());
                    }
                    return $toolFound;
                });
                if ($selectedTool) {
                    $myTool = $selectedTool[0];
                    Log::debug("We have a tool on this page, it contains at least a name: " . $myTool->name . " (" . $myTool->id . ")");
                    $tools[] = $myTool;
                    $this->toolService->attachDataSource($myTool->id, $sourceId);
                    foreach ($dataTypes as $dataType) {
                        $rdfFullUri = $dataType->rdf_mapping;
                        if (Str::startsWith($rdfFullUri, $vocab)) {
                            $rdfFullUri = str_replace($vocab, "", $rdfFullUri);
                        }
                        $node->filterXPath('//*[@property="' . $rdfFullUri . '" or @property="'.$vocab.$rdfFullUri.'"]')->each(function (Crawler $subNode) use ($dataType, $myTool, $sourceId, $rdfFullUri) {
                            Log::debug($dataType->rdf_mapping . " --> " . $subNode->text());
                            $dataFound = $this->dataService->findByValueAndTool($myTool->id, $subNode->text());
                            if (!$dataFound) {
                                $correctWithDataTypeOption = false;
                                if ($dataType->dataTypeOption()->count() > 0) {
                                    foreach ($dataType->dataTypeOption()->get() as $dataTypeOption) {
                                        if (BaseHelper::generateSlug(trim(preg_replace('/\s+/', ' ', $subNode->text()))) == BaseHelper::generateSlug($dataTypeOption->value)) {
                                            $correctWithDataTypeOption = true;
                                        }
                                    }
                                } else {
                                    $correctWithDataTypeOption = true;
                                }
                                if ($correctWithDataTypeOption) {
                                    $d = new Data;
                                    $d->fill([
                                        "value" => $subNode->text(),
                                        "tool_id" => $myTool->id,
                                        "data_type_id" => $dataType->id,
                                        "data_source_id" => $sourceId,
                                        "user_id" => Auth::user()->id,
                                        "created_at" => new DateTime,
                                        "updated_at" => new DateTime
                                    ]);
                                    $d->save();
                                    Log::debug("Saved data id:" . $d->id);
                                }
                            }
                        });
                    }
                    if ($myTool->isFilledSingle()) {
                        $myTool->is_filled = true;
                        $toolsFullyDescribed[] = $myTool;
                        $myTool->save();
                    }
                }
            }
        });
//        $harvester->last_launched = date("Y-m-d H:i:s");
        $harvester->launch_now = 0;
        $harvester->save();
    }

    /**
     * AVAILABLE SCHEDULES
     */

    protected function everyFiveMinutes(callable $callback)
    {
        if((int) date('i', $this->timestamp) % 5 === 0) call_user_func($callback);
    }

    protected function everyTenMinutes(callable $callback)
    {
        if((int) date('i', $this->timestamp) % 10 === 0) call_user_func($callback);
    }

    protected function everyFifteenMinutes(callable $callback)
    {
        if((int) date('i', $this->timestamp) % 15 === 0) call_user_func($callback);
    }

    protected function everyThirtyMinutes(callable $callback)
    {
        if((int) date('i', $this->timestamp) % 30 === 0) call_user_func($callback);
    }

    /**
     * Called every full hour
     */
    protected function hourly(callable $callback)
    {
        if(date('i', $this->timestamp) === '00') call_user_func($callback);
    }

    /**
     * Called every hour at the minute specified
     *
     * @param  integer $minute
     */
    protected function hourlyAt($minute, callable $callback)
    {
        if((int) date('i', $this->timestamp) === $minute) call_user_func($callback);
    }

    /**
     * Called every day
     */
    protected function daily(callable $callback)
    {
        if(date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    /**
     * Called every day at the 24h-format time specified
     *
     * @param  string $time [HH:MM]
     */
    protected function dailyAt($time, callable $callback)
    {
        if(date('H:i', $this->timestamp) === $time) call_user_func($callback);
    }

    /**
     * Called every day at 12:00am and 12:00pm
     */
    protected function twiceDaily(callable $callback)
    {
        if(date('h:i', $this->timestamp) === '12:00') call_user_func($callback);
    }

    /**
     * Called every weekday
     */
    protected function weekdays(callable $callback)
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        if(in_array(date('D', $this->timestamp), $days) && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function mondays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Mon' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function tuesdays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Tue' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function wednesdays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Wed' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function thursdays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Thu' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function fridays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Fri' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function saturdays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Sat' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    protected function sundays(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Sun' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    /**
     * Called once every week (basically the same as using sundays() above...)
     */
    protected function weekly(callable $callback)
    {
        if(date('D', $this->timestamp) === 'Sun' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    /**
     * Called once every week at the specified day and time
     *
     * @param  string $day  [Three letter format (Mon, Tue, ...)]
     * @param  string $time [HH:MM]
     */
    protected function weeklyOn($day, $time, callable $callback)
    {
        if(date('D', $this->timestamp) === $day && date('H:i', $this->timestamp) === $time) call_user_func($callback);
    }

    /**
     * Called each month on the 1st
     */
    protected function monthly(callable $callback)
    {
        if(date('d', $this->timestamp) === '01' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

    /**
     * Called each year on the 1st of January
     */
    protected function yearly(callable $callback)
    {
        if(date('m', $this->timestamp) === '01' && date('d', $this->timestamp) === '01' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
    }

}