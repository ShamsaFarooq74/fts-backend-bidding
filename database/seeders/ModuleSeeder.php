<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use Carbon\Carbon;
class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [ 'name'=> 'user',],
            ['name'=> 'permission',],
            ['name'=> 'role',],
            ['name'=> 'vehicles',],
            ['name'=> 'company',],
            ['name'=> 'faqs', ],
            ['name'=> 'reviews',],
            ['name'=> 'destinations',],
            ['name'=> 'services',],
            ['name'=> 'contactUs',],
            ['name'=> 'booking',],
            ['name'=> 'subscritpion',],     
        ];
        $now = Carbon::now();
        foreach ($data as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
          }
        Module::insert($data);
    }
}
