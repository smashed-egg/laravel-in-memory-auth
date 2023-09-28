<?php

namespace SmashedEgg\LaravelInMemoryAuth\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Hashing\Hasher;

class HashPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smashed-egg:hash:password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash a password';

    public function __construct(protected Hasher $hasher)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->output->writeln($this->hasher->make($this->argument('password')));

        return 0;
    }
}
