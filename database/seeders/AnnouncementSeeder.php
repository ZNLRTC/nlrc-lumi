<?php

namespace Database\Seeders;

// use App\Mail\SendAnnouncementEmail;
use App\Models\Trainee;
use App\Models\Announcement;
use App\Notifications\AnnouncementNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Announcement::insert([
            [
                'user_id' => 1,
                'title' => 'Markdown Test',
                'description' => 'I am **bold** but I am *italicized*',
                'created_at' => Carbon::now()->subMinutes(15),
                'updated_at' => Carbon::now()->subMinutes(15)
            ],
            [
                'user_id' => 2,
                'title' => 'Long Announcement Test',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas rhoncus interdum nisi. Duis libero nunc, blandit in arcu sed, lobortis molestie orci. Fusce facilisis tellus risus, at pharetra justo cursus nec. Nulla facilisis arcu libero, et tempor dui bibendum et. Sed porta odio ac nibh luctus, ut elementum arcu venenatis. Duis commodo cursus massa, vel fermentum nunc congue vel. Vivamus imperdiet neque libero, tempor semper lacus bibendum ultricies. Pellentesque quam turpis, iaculis in erat vel, fringilla ultrices massa. Sed dapibus est et dictum mattis. Vivamus maximus eget quam a pretium. Sed in velit libero. Integer fermentum malesuada fringilla. Nam porttitor neque sit amet risus egestas, id posuere felis porttitor. Sed vulputate mattis sapien, id aliquam ligula tempus consequat. Aliquam nec erat ac ex convallis vestibulum. Vivamus hendrerit mi lobortis, hendrerit odio porttitor, consequat libero. Nullam malesuada est vitae ultrices hendrerit. In eu sapien sagittis, mollis enim eget, pulvinar mauris. Nam id semper nisi. Integer nec velit nec lacus consequat viverra vel sit amet purus. Proin quis ante in nibh tempus porta non in lacus. Phasellus et cursus diam, et mattis tellus. Phasellus efficitur, sapien vitae hendrerit mattis, purus lacus egestas turpis, at hendrerit massa dolor in nisi. Sed consequat augue in tellus porttitor, sit amet vehicula lectus suscipit. Phasellus imperdiet est ac lorem semper, nec placerat dui ultrices. Vivamus nec leo ornare, malesuada ligula pellentesque, ultricies dolor. Phasellus vitae eleifend augue. Proin blandit, nunc sit amet auctor ullamcorper, nulla erat auctor lorem, a efficitur justo ligula eu turpis. Nam eu tincidunt nisi. Suspendisse justo libero, aliquet porttitor dui ac, molestie.',
                'created_at' => Carbon::now()->subMinutes(10),
                'updated_at' => Carbon::now()->subMinutes(10)
            ],
            [
                'user_id' => 3,
                'title' => 'Test is_priority',
                'description' => 'This must show up as the latest announcement on the dashboard if this value is 1 otherwise, the latest announcement whose is_priority is 0 would show up instead.',
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5)
            ],
            [
                'user_id' => 4,
                'title' => 'Test is_read',
                'description' => 'This must show up as a blue envelope on the notification bell icon since this value is 1. For this to work, you must view this announcement on the notification bell on your dashboard',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        $announcements = Announcement::all();

        foreach ($announcements as $announcement) {
            $trainee = Trainee::findOrFail(1);

            /*
            For demo purposes only (hence it's in comments), below is a sample
            Mail function to send email to a trainee before notifying him/her of the announcement in the site
            We don't need to enable this since we are just seeding fake data
            */
            // Mail::to($trainee->email)->send(new SendAnnouncementEmail($announcement, $trainee));

            $trainee->notify(new AnnouncementNotification($announcement, $announcement['id'] == 3 ? 1 : 0));
        }
    }
}
