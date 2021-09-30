<?php

namespace Laravel\Horizon\Console;

use Illuminate\Console\Command;
use Laravel\Horizon\Contracts\JobRepository;

class ForgetFailedCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'horizon:forget {id : The ID of the failed job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a failed queue job';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(JobRepository $repository)
    {
        $failedJobPayload = $this->laravel['queue.failer']->find($this->argument('id'))->payload ?? null;
        if (! is_null($failedJobPayload)) {
            $jobId = json_decode($failedJobPayload, true)['uuid'] ?? $this->argument('id');
            $repository->deleteFailed($jobId);
        }

        if ($this->laravel['queue.failer']->forget($this->argument('id'))) {
            $this->info('Failed job deleted successfully!');
        } else {
            $this->error('No failed job matches the given ID.');
        }
    }
}
