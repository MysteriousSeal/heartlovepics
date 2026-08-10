<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignImageUsersSeeder extends Seeder
{
    /** @var list<string> */
    private const USERNAMES = [
        'aurora',
        'blake',
        'coral',
        'dune',
        'ember',
        'fable',
        'grove',
        'haven',
        'indigo',
        'juniper',
        'kepler',
        'lumen',
        'meadow',
        'nova',
        'orion',
        'prism',
        'quill',
        'river',
        'solstice',
        'terra',
    ];

    public function run(): void
    {
        $userIds = collect(self::USERNAMES)
            ->map(function (string $username) {
                return User::query()->updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $username,
                        'email' => null,
                        'password' => 'password',
                        'is_admin' => false,
                    ],
                )->id;
            })
            ->values()
            ->all();

        Image::query()
            ->orderBy('id')
            ->each(function (Image $image) use ($userIds): void {
                $image->update([
                    'user_id' => $userIds[array_rand($userIds)],
                ]);
            });
    }
}