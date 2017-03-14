<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $faker = Faker\Factory::create();
        $availability = ['Free', 'Busy', 'Unavailable'];
        $privacy = ['Private', 'Circle', 'Public'];
        $type = ['Family', 'Friends', 'Work'];
        for($i = 0; $i<100; $i++){
            $task = [
                'user_id' => App\User::all()->random()->id,
                'title' => $faker->sentence,
                'from' => $faker->dateTimeBetween(Carbon::today(), new Carbon('next friday')),
                'to' => $faker->dateTimeBetween(new Carbon('next friday'),
                    (new Carbon('next friday'))->addWeeks(1)),
                'availability' => $availability[array_rand($availability)],
                'privacy' => $privacy[array_rand($privacy)],
                'type' => $type[array_rand($type)],
                'location' => $faker->address,
                'detail' => $faker->paragraph,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $task_id = DB::table('tasks')->insertGetId($task);
            $r = rand(1, 4);
            if ($r === 1) {
                DB::table('daily_tasks')->insert([
                    'task_id' => $task_id,
                    'repetition' => rand(1, 31),
                    'created_at' => new Carbon,
                    'updated_at' => new Carbon,
                ]);
            }elseif ($r === 2) {
                DB::table('weekly_tasks')->insert([
                    'task_id' => $task_id,
                    'repetition' => rand(1, 4),
                    'created_at' => new Carbon,
                    'updated_at' => new Carbon,
                ]);

                $days = collect([1, 2, 3, 4, 5, 6, 7])->random(3);

                foreach ($days as $key => $day) {
                    DB::table('weekly_repetitions')->insert([
                        'task_id' => $task_id,
                        'week_day' => $day,
                        'created_at' => new Carbon,
                        'updated_at' => new Carbon,
                    ]);
                }
            }elseif ($r===3) {
                DB::table('monthly_tasks')->insert([
                    'task_id' => $task_id,
                    'repetition' => rand(1, 12),
                    'created_at' => new Carbon,
                    'updated_at' => new Carbon,
                ]);
            }else {
                DB::table('yearly_tasks')->insert([
                    'task_id' => $task_id,
                    'repetition' => rand(1, 12),
                    'created_at' => new Carbon,
                    'updated_at' => new Carbon,
                ]);
            }
        }

        $tasks = App\Task::all();
        $users = App\User::all();
        $tags = App\Tag::all();
        foreach ($tasks as $key => $task) {

            $rand_users = $users->random(3);
            $rand_users = $rand_users->diff(collect([$task->user]));

            foreach ($rand_users as $key => $value) {
                DB::table('associations')->insert([
                    'task_id' => $task->id,
                    'user_id' => $value->id,
                    'is_approved' => rand(0, 1),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $rand_tags = $tags->random(3);

            foreach ($rand_tags as $key => $tag) {
                DB::table('tag_task')->insert([
                    'tag_id' => $tag->id,
                    'task_id' => $task->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
