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
                'password' => Hash::make('admin@kukuysontop'),
            ],
            [
                'name' => 'Kuku',
                'email' => 'kukudota2@kukuys.live',
                'password' => Hash::make('kukudota2@kukuysontop'),
            ],
            [
                'name' => 'Armel',
                'email' => 'armeldoto@kukuys.live',
                'password' => Hash::make('armeldoto@kukuysontop'),
            ],
            [
                'name' => 'Dj',
                'email' => 'djdoto@kukuys.live',
                'password' => Hash::make('djdoto@kukuysontop'),
            ],
            [
                'name' => 'Yowe',
                'email' => 'yowe@kukuys.live',
                'password' => Hash::make('yowe@kukuysontop'),
            ],
            [
                'name' => 'Palos',
                'email' => 'solapsapdota@kukuys.live',
                'password' => Hash::make('palos@kukuysontop'),
            ],
            [
                'name' => 'Kokz',
                'email' => 'kokzdota@kukuys.live',
                'password' => Hash::make('kokz@kukuysontop'),
            ],
            [
                'name' => 'Abat',
                'email' => 'abatdota@kukuys.live',
                'password' => Hash::make('abat@kukuysontop'),
            ],
            [
                'name' => 'Jwl',
                'email' => 'jwldota@kukuys.live',
                'password' => Hash::make('jwl@kukuysontop'),
            ],
            [
                'name' => 'Tino',
                'email' => 'tinodota@kukuys.live',
                'password' => Hash::make('tino@kukuysontop'),
            ],
            [
                'name' => 'Karl',
                'email' => 'karldotaa@kukuys.live',
                'password' => Hash::make('karl@kukuysontop'),
            ],
            [
                'name' => 'JG',
                'email' => 'jgdota@kukuys.live',
                'password' => Hash::make('jgdota@kukuysontop'),
            ],
            [
                'name' => 'Sep',
                'email' => 'sepdoto@kukuys.live',
                'password' => Hash::make('sep@kukuysontop'),
            ],
            [
                'name' => 'Lewis',
                'email' => 'lewisdotaa@kukuys.live',
                'password' => Hash::make('lewis@kukuysontop'),
            ],
            [
                'name' => 'Natsumi',
                'email' => 'natsumidota@kukuys.live',
                'password' => Hash::make('natsumi@kukuysontop'),
            ],
            [
                'name' => 'Nikko',
                'email' => 'nikkodota2@kukuys.live',
                'password' => Hash::make('nikkodota2@kukuysontop'),
            ],
            [
                'name' => 'Jaunuel',
                'email' => 'jaunueldota@kukuys.live',
                'password' => Hash::make('jaunueldota@kukuysontop'),
            ],
            [
                'name' => 'Jing',
                'email' => 'jingdota@kukuys.live',
                'password' => Hash::make('jing@kukuysontop'),
            ],
            [
                'name' => 'JTZ',
                'email' => 'jtzcast@kukuys.live',
                'password' => Hash::make('jtzcast@kukuysontop'),
            ],
            [
                'name' => 'Nevertheless',
                'email' => 'nevertheless@kukuys.live',
                'password' => Hash::make('nevertheless@kukuysontop'),
            ],
            [
                'name' => 'Lash',
                'email' => 'lashsegway28@kukuys.live',
                'password' => Hash::make('lashsegway28@kukuysontop'),
            ],
            [
                'name' => 'Jabolero',
                'email' => 'jabolerodota@kukuys.live',
                'password' => Hash::make('jabolero@kukuysontop'),
            ],
            [
                'name' => 'Chupaeng',
                'email' => 'chupaeng@kukuys.live',
                'password' => Hash::make('chupaeng@kukuysontop'),
            ],
            [
                'name' => 'Sunshine',
                'email' => 'sunshinemelodyyy@kukuys.live',
                'password' => Hash::make('sunshinemelodyyy@kukuysontop'),
            ],
            [
                'name' => 'Hubris',
                'email' => 'hubrisss@kukuys.live',
                'password' => Hash::make('hubrisss@kukuysontop'),
            ],
        ]);
    }
}
