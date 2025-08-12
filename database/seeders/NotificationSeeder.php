<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Utility\Enums\NotificationStatusEnum;
use App\Utility\Enums\NotificationTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('type', 'user')->take(5)->get();

        // General notifications for all users
        $generalNotifications = [
            [
                'target_id' => null,
                'en_title' => 'Welcome to Our Platform',
                'ar_title' => 'مرحباً بك في منصتنا',
                'en_body' => 'Thank you for joining our platform. We hope you have a great experience with us.',
                'ar_body' => 'شكراً لك لانضمامك لمنصتنا. نتمنى أن تحصل على تجربة رائعة معنا.',
                'target_type' => NotificationTypeEnum::ALL->value,
                'status' => NotificationStatusEnum::SENT->value,
                'scheduled_at' => null,
            ],
            [
                'target_id' => null,
                'en_title' => 'System Maintenance',
                'ar_title' => 'صيانة النظام',
                'en_body' => 'Our system will undergo maintenance tonight from 2 AM to 4 AM UTC. Some features may be unavailable during this time.',
                'ar_body' => 'سيخضع نظامنا للصيانة الليلة من الساعة 2 صباحاً إلى 4 صباحاً بتوقيت جرينيتش. قد تكون بعض الميزات غير متاحة خلال هذا الوقت.',
                'target_type' => NotificationTypeEnum::ALL->value,
                'status' => NotificationStatusEnum::PENDING->value,
                'scheduled_at' => Carbon::now()->addDays(1)->setHour(2)->setMinute(0),
            ],
            [
                'target_id' => null,
                'en_title' => 'New Features Available',
                'ar_title' => 'ميزات جديدة متاحة',
                'en_body' => 'We have added new exciting features to enhance your experience. Check them out in the latest update.',
                'ar_body' => 'لقد أضفنا ميزات جديدة ومثيرة لتحسين تجربتك. تحقق منها في التحديث الأخير.',
                'target_type' => NotificationTypeEnum::ALL->value,
                'status' => NotificationStatusEnum::SENT->value,
                'scheduled_at' => null,
            ],
        ];

        foreach ($generalNotifications as $notification) {
            Notification::create($notification);
        }

        // Specific user notifications
        if ($users->count() > 0) {
            $specificNotifications = [
                [
                    'target_id' => $users->first()->id,
                    'en_title' => 'Profile Completion',
                    'ar_title' => 'إكمال الملف الشخصي',
                    'en_body' => 'Please complete your profile to get the most out of our platform.',
                    'ar_body' => 'يرجى إكمال ملفك الشخصي للحصول على أقصى استفادة من منصتنا.',
                    'target_type' => NotificationTypeEnum::SPECIFIC_USER->value,
                    'status' => NotificationStatusEnum::SENT->value,
                    'scheduled_at' => null,
                ],
                [
                    'target_id' => $users->skip(1)->first()?->id ?? $users->first()->id,
                    'en_title' => 'Account Verification',
                    'ar_title' => 'التحقق من الحساب',
                    'en_body' => 'Your account has been successfully verified. You now have access to all features.',
                    'ar_body' => 'تم التحقق من حسابك بنجاح. لديك الآن حق الوصول لجميع الميزات.',
                    'target_type' => NotificationTypeEnum::SPECIFIC_USER->value,
                    'status' => NotificationStatusEnum::SENT->value,
                    'scheduled_at' => null,
                ],
                [
                    'target_id' => $users->skip(2)->first()?->id ?? $users->first()->id,
                    'en_title' => 'Reminder: Update Your Password',
                    'ar_title' => 'تذكير: قم بتحديث كلمة المرور',
                    'en_body' => 'For your security, we recommend updating your password regularly.',
                    'ar_body' => 'من أجل أمانك، ننصح بتحديث كلمة المرور بانتظام.',
                    'target_type' => NotificationTypeEnum::SPECIFIC_USER->value,
                    'status' => NotificationStatusEnum::PENDING->value,
                    'scheduled_at' => Carbon::now()->addHours(2),
                ],
            ];

            foreach ($specificNotifications as $notification) {
                if ($notification['target_id']) {
                    Notification::create($notification);
                }
            }
        }

        // Scheduled notifications for future
        $scheduledNotifications = [
            [
                'target_id' => null,
                'en_title' => 'Weekly Newsletter',
                'ar_title' => 'النشرة الإخبارية الأسبوعية',
                'en_body' => 'Check out this week\'s highlights and new features in our weekly newsletter.',
                'ar_body' => 'تحقق من أبرز أحداث هذا الأسبوع والميزات الجديدة في نشرتنا الإخبارية الأسبوعية.',
                'target_type' => NotificationTypeEnum::ALL->value,
                'status' => NotificationStatusEnum::SCHEDULED->value,
                'scheduled_at' => Carbon::now()->addWeek(),
            ],
            [
                'target_id' => null,
                'en_title' => 'Happy Weekend!',
                'ar_title' => 'عطلة نهاية أسبوع سعيدة!',
                'en_body' => 'Have a great weekend! Don\'t forget to check out our weekend special offers.',
                'ar_body' => 'أتمنى لك عطلة نهاية أسبوع رائعة! لا تنس تصفح عروضنا الخاصة لنهاية الأسبوع.',
                'target_type' => NotificationTypeEnum::ALL->value,
                'status' => NotificationStatusEnum::SCHEDULED->value,
                'scheduled_at' => Carbon::now()->addDays(5), // This Friday
            ],
        ];

        foreach ($scheduledNotifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Created notifications:');
        $this->command->info('- 3 General notifications (for all users)');
        $this->command->info('- 3 Specific user notifications');
        $this->command->info('- 2 Scheduled notifications');
        $this->command->info('Total: ' . Notification::count() . ' notifications');
    }
}
