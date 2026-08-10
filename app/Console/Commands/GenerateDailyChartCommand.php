<?php

namespace App\Console\Commands;

use App\Actions\Charts\GenerateDailyChart;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateDailyChartCommand extends Command
{
    protected $signature = 'charts:generate-daily {date? : Vote date to tally (YYYY-MM-DD), defaults to yesterday}';

    protected $description = "Generate the daily chart snapshot from a day's votes.";

    public function handle(GenerateDailyChart $action): int
    {
        $date = $this->argument('date')
            ? Carbon::parse($this->argument('date'))
            : now()->subDay();

        try {
            Log::info('Generating daily chart.', ['date' => $date->toDateString()]);

            $chart = $action($date);

            Log::info('Daily chart generated.', [
                'date' => $date->toDateString(),
                'entries' => $chart->entries()->count(),
            ]);

            $this->info("Daily chart generated for {$date->toDateString()}.");

            return self::SUCCESS;
        } catch (LockTimeoutException) {
            $this->warn("Chart generation for {$date->toDateString()} is already running elsewhere; not waiting further.");

            return self::SUCCESS;
        } catch (Throwable $e) {
            Log::error('Daily chart generation failed.', [
                'date' => $date->toDateString(),
                'exception' => $e->getMessage(),
            ]);

            $this->error("Chart generation failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
