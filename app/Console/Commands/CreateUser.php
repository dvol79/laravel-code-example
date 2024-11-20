<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {email : the email of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [];

        try {
            $data['email'] = (string) $this->argument('email');
            $data['name'] = $this->ask('Enter user name (REQUIRED!)');
            $data['role'] = $this->choice(
                'Select role',
                [User::ROLE_USER, User::ROLE_ADMIN],
                User::ROLE_USER
            );
            $data['bdate'] = $this->ask('Enter user birth date (Format: YYYY-MM-DD!)');
            $data['password'] = $this->secret('Enter password (Min 6 characters: letters|numbers|symbols');

            $validator = Validator::make($data, [
                'email'    => ['required','email', 'unique:users'],
                'name'     => ['required', 'min:2'],
                'role'     => Rule::in([User::ROLE_USER, User::ROLE_ADMIN]),
                'bdate'    => ['required', 'date'],
                'password' => ['required', Password::min(6)->letters()->numbers()],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $this->error($message);
                }

                exit;
            }

            /** @var User $user */
            $user = User::create([
                'name'           => $data['name'],
                'email'          => $data['email'],
                'password'       => Hash::make($data['password']),
                'remember_token' => Str::random(10),
                'role'           => $data['role'],
                'bdate'          => $data['bdate'],
            ]);

            $user->markEmailAsVerified();
            event(new Verified($user));

            $this->info("User created successfully with data:");
            $this->table(
                ['Email', 'Name', 'Role', 'BDate', 'Password'],
                [array_values($data)]
            );
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            
            exit;
        }
    }
}
