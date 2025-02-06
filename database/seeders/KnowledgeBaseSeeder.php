<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KnowledgeBase\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kb_categories')->insert([
            ['name' => 'Meetings', 'slug' => 'meetings'],
            ['name' => 'Documents', 'slug' => 'documents'],
            ['name' => 'Groups', 'slug' => 'groups'],
            ['name' => 'Materials', 'slug' => 'materials'],
            ['name' => 'Grading', 'slug' => 'grading'],
            ['name' => 'Tech issues', 'slug' => 'tech-issues'],
        ]);

        // Insert a premade article just to test markdown
        DB::table('kb_articles')->insert([
            [
                'category_id' => 1,
                'title' => 'How to schedule a meeting',
                'summary' => 'Learn about the steps needed to schedule a meeting.',
                'content' => 'Iste enim ea aut **consequatur**. Ullam et ullam deleniti aut ipsa. Iste quia autem modi est est voluptatem eaque iste.

Corporis ab amet quas et mollitia aperiam. Sit aliquid totam aut omnis. At quod qui enim qui. Minima asperiores omnis nemo placeat laborum.

* List item1
* List item 2
* List items 3

Voluptates voluptatum dolorem voluptate. Dolores vitae sint est qui exercitationem saepe vel. Qui ullam *illo* in saepe architecto et.',
                'status' => 'Published',
                'slug' => 'how-to-schedule-a-meeting',
                'audiences' => json_encode(["Trainee", "Instructor"]),
            ],
        ]);

        {
            Article::factory()->count(20)->create();
        }

    }
}
