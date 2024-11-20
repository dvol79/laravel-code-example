<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:force-delete
                            {id : the ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete user from DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $user = $this->getUser();
            $userName = $user->name;

            if (!$user->forceDelete()) {
                $this->error('User deletion error!');
                exit;
            }

            $this->info("User {$userName} successfully deleted!");
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            exit;
        }
    }

    /**
     * Return user
     *
     * @return User
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     * @throws Exception
     */
    private function getUser(): User
    {
        return User::withTrashed()
            ->findOrFail($this->argument('id'));
    }
}
