<?php

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class getToken extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:token';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generates Lyft Token.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */



	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{

		return $scheduler->daily();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		$request = Request::create('tokens/update', 'GET');
		return Route::dispatch($request)->getContent();

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */


}
