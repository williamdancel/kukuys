<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Kukuys Admin',
                'email' => 'admin@kukuys.live',
                'password' => Hash::make('kukuysontop2026!'),
            ],
            [
                'name' => 'Kuku',
                'email' => 'kukudota2@kukuys.live',
                'password' => Hash::make('kuku@kukuysontop2026!'),
            ],
            [
                'name' => 'Armel',
                'email' => 'armeldoto@kukuys.live',
                'password' => Hash::make('armel@kukuysontop2026!'),
            ],
            [
                'name' => 'Dj',
                'email' => 'djdoto@kukuys.live',
                'password' => Hash::make('dj@kukuysontop2026!'),
            ],
            [
                'name' => 'Yowe',
                'email' => 'yowe@kukuys.live',
                'password' => Hash::make('yowe@kukuysontop2026!'),
            ],
            [
                'name' => 'Palos',
                'email' => 'solapsapdota@kukuys.live',
                'password' => Hash::make('palos@kukuysontop2026!'),
            ],
            [
                'name' => 'Kokz',
                'email' => 'kokzdota@kukuys.live',
                'password' => Hash::make('kokz@kukuysontop2026!'),
            ],
            [
                'name' => 'Abat',
                'email' => 'abatdota@kukuys.live',
                'password' => Hash::make('abat@kukuysontop2026!'),
            ],
            [
                'name' => 'Jwl',
                'email' => 'jwldota@kukuys.live',
                'password' => Hash::make('jwl@kukuysontop2026!'),
            ],
            [
                'name' => 'Tino',
                'email' => 'tinodota@kukuys.live',
                'password' => Hash::make('tino@kukuysontop2026!'),
            ],
            [
                'name' => 'Karl',
                'email' => 'karldotaa@kukuys.live',
                'password' => Hash::make('karl@kukuysontop2026!'),
            ],
            [
                'name' => 'JG',
                'email' => 'jgdota@kukuys.live',
                'password' => Hash::make('jg@kukuysontop2026!'),
            ],
            [
                'name' => 'Sep',
                'email' => 'sepdoto@kukuys.live',
                'password' => Hash::make('sep@kukuysontop2026!'),
            ],
            [
                'name' => 'Lewis',
                'email' => 'lewisdotaa@kukuys.live',
                'password' => Hash::make('lewis@kukuysontop2026!'),
            ],
            [
                'name' => 'Natsumi',
                'email' => 'natsumidota@kukuys.live',
                'password' => Hash::make('natsumi@kukuysontop2026!'),
            ],
            [
                'name' => 'Nikko',
                'email' => 'nikkodota2@kukuys.live',
                'password' => Hash::make('nikko@kukuysontop2026!'),
            ],
            [
                'name' => 'Jaunuel',
                'email' => 'jaunueldota@kukuys.live',
                'password' => Hash::make('jaunuel@kukuysontop2026!'),
            ],
            [
                'name' => 'Jing',
                'email' => 'jingdota@kukuys.live',
                'password' => Hash::make('jing@kukuysontop2026!'),
            ],
            [
                'name' => 'JTZ',
                'email' => 'jtzcast@kukuys.live',
                'password' => Hash::make('jtz@kukuysontop2026!'),
            ],
            [
                'name' => 'Nevertheless',
                'email' => 'nevertheless@kukuys.live',
                'password' => Hash::make('nevertheless@kukuysontop2026!'),
            ],
            [
                'name' => 'Lash',
                'email' => 'lashsegway28@kukuys.live',
                'password' => Hash::make('lash@kukuysontop2026!'),
            ],
            [
                'name' => 'Jabolero',
                'email' => 'jabolerodota@kukuys.live',
                'password' => Hash::make('jabolero@kukuysontop2026!'),
            ],
            [
                'name' => 'Chupaeng',
                'email' => 'chupaeng@kukuys.live',
                'password' => Hash::make('chupaeng@kukuysontop2026!'),
            ],
            [
                'name' => 'Sunshine',
                'email' => 'sunshinemelodyyy@kukuys.live',
                'password' => Hash::make('sunshine@kukuysontop2026!'),
            ],
            [
                'name' => 'Hubris',
                'email' => 'hubrisss@kukuys.live',
                'password' => Hash::make('hubris@kukuysontop2026!'),
            ],
        ]);
    }
}
