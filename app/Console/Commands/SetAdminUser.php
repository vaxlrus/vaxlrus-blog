<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SetAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-admin-user {userId} {--R|removeRole}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new admin user to web site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = (int) $this->argument('userId');
        $removeAdminRole = $this->option('removeRole');

        $user = User::findOrFail($userId);

        if (! $user)
        {
            $this->error("Не найден пользователь с ID=$userId");
            return Command::INVALID;
        }

        if ($removeAdminRole === true)
        {
            $user->changeAdminRole(false);
            $user->save();

            $this->comment("Пользователь $user->name ($user->id) удален из администраторов");

            return Command::SUCCESS;
        }

        if ($user->is_admin)
        {
            $this->info("Пользователь $user->name ($user->id) уже является администратором");

            return Command::SUCCESS;
        }

        $user->changeAdminRole(true);
        $user->save();

        $this->comment("Права администратора выданы пользователю: $user->name ($user->id)");

        return Command::SUCCESS;
    }
}
