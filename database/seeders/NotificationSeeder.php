<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            // Create 0-5 notifications for each user
            $notificationCount = rand(0, 5);
            
            Notification::factory()
                ->count($notificationCount)
                ->create([
                    'user_id' => $user->id,
                ]);
        }
    }
}