<?php

namespace Database\Seeders;

use App\Models\CampaignAssignment;
use App\Models\Club;
use App\Models\League;
use App\Models\Player;
use App\Models\User;
use App\Models\VotingCampaign;
use App\Models\VotingCampaignTarget;
use App\Models\VotingQuestion;
use App\Models\VotingQuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            'manage-clubs', 'view-clubs',
            'manage-admins', 'view-admins',
            'manage-players', 'view-players', 'import-players',
            'manage-campaigns', 'view-campaigns',
            'manage-questions', 'view-questions',
            'send-campaigns', 'view-assignments',
            'manage-voting-links', 'view-voting-links',
            'view-all-results', 'view-club-results',
            'export-reports',
            'view-audit-logs',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions($permissions);

        $clubAdmin = Role::firstOrCreate(['name' => 'club-admin']);
        $clubAdmin->syncPermissions([
            'view-clubs',
            'manage-players', 'view-players', 'import-players',
            'view-campaigns',
            'view-assignments',
            'manage-voting-links', 'view-voting-links',
            'view-club-results',
        ]);

        // Create Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@sportsvoting.com'],
            [
                'name' => 'مدير الجمعية',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super-admin');

        // Create Demo Leagues
        $league1 = League::firstOrCreate(
            ['name_ar' => 'دوري روشن السعودي للمحترفين'],
            ['name_en' => 'Saudi Pro League', 'season' => '2025-2026', 'description' => 'الدوري السعودي للمحترفين', 'is_active' => true]
        );

        $league2 = League::firstOrCreate(
            ['name_ar' => 'دوري الدرجة الأولى'],
            ['name_en' => 'First Division League', 'season' => '2025-2026', 'description' => 'دوري الدرجة الأولى السعودي', 'is_active' => true]
        );

        // Create Demo Clubs
        $clubs = [
            ['league_id' => $league1->id, 'name_ar' => 'نادي الهلال', 'name_en' => 'Al Hilal FC', 'contact_email' => 'info@alhilal.com', 'contact_phone' => '0112345678'],
            ['league_id' => $league1->id, 'name_ar' => 'نادي النصر', 'name_en' => 'Al Nassr FC', 'contact_email' => 'info@alnassr.com', 'contact_phone' => '0112345679'],
            ['league_id' => $league1->id, 'name_ar' => 'نادي الأهلي', 'name_en' => 'Al Ahli FC', 'contact_email' => 'info@alahli.com', 'contact_phone' => '0112345680'],
            ['league_id' => $league2->id, 'name_ar' => 'نادي الاتحاد', 'name_en' => 'Al Ittihad FC', 'contact_email' => 'info@alittihad.com', 'contact_phone' => '0112345681'],
        ];

        $positions = ['حارس مرمى', 'مدافع', 'وسط', 'مهاجم'];
        $nationalities = ['سعودي', 'مصري', 'برازيلي', 'أرجنتيني'];

        foreach ($clubs as $index => $clubData) {
            $club = Club::firstOrCreate(
                ['name_ar' => $clubData['name_ar']],
                $clubData
            );

            // Create Club Admin
            $adminUser = User::firstOrCreate(
                ['email' => 'admin' . ($index + 1) . '@club.com'],
                [
                    'name' => 'إداري ' . $clubData['name_ar'],
                    'password' => Hash::make('password'),
                    'club_id' => $club->id,
                    'phone' => '05' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $adminUser->assignRole('club-admin');

            // Create 10 Players per club
            for ($i = 1; $i <= 10; $i++) {
                Player::firstOrCreate(
                    ['club_id' => $club->id, 'national_id' => '10' . $club->id . str_pad($i, 4, '0', STR_PAD_LEFT)],
                    [
                        'name' => 'لاعب ' . $i . ' - ' . $clubData['name_ar'],
                        'player_number' => (string) $i,
                        'phone' => '05' . str_pad(($club->id * 100) + $i, 8, '0', STR_PAD_LEFT),
                        'nationality' => $nationalities[array_rand($nationalities)],
                        'position' => $positions[array_rand($positions)],
                        'status' => 'active',
                    ]
                );
            }
        }

        // Create a demo voting campaign
        $campaign = VotingCampaign::firstOrCreate(
            ['title' => 'تصويت أفضل لاعب للموسم 2025-2026'],
            [
                'description' => 'حملة تصويت لاختيار أفضل لاعب في الموسم الرياضي 2025-2026',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
                'status' => 'active',
                'target_audience' => 'all',
                'single_response' => true,
                'prevent_edit_after_submit' => true,
                'show_countdown' => true,
                'allow_review_before_submit' => true,
                'created_by' => $superAdminUser->id,
            ]
        );

        // Add questions
        $q1 = VotingQuestion::firstOrCreate(
            ['campaign_id' => $campaign->id, 'title' => 'من هو أفضل لاعب في الموسم؟'],
            [
                'type' => 'radio',
                'is_required' => true,
                'sort_order' => 1,
            ]
        );

        $q1Options = ['اللاعب أحمد', 'اللاعب محمد', 'اللاعب خالد', 'اللاعب عبدالله'];
        foreach ($q1Options as $i => $opt) {
            VotingQuestionOption::firstOrCreate(
                ['question_id' => $q1->id, 'value' => 'player_' . ($i + 1)],
                ['label' => $opt, 'sort_order' => $i + 1]
            );
        }

        $q2 = VotingQuestion::firstOrCreate(
            ['campaign_id' => $campaign->id, 'title' => 'ما هي أفضل المراكز أداءً هذا الموسم؟'],
            [
                'type' => 'checkbox',
                'is_required' => true,
                'sort_order' => 2,
                'max_selections' => 2,
                'description' => 'اختر مركزين كحد أقصى',
            ]
        );

        $q2Options = ['حراسة المرمى', 'خط الدفاع', 'خط الوسط', 'خط الهجوم'];
        foreach ($q2Options as $i => $opt) {
            VotingQuestionOption::firstOrCreate(
                ['question_id' => $q2->id, 'value' => 'position_' . ($i + 1)],
                ['label' => $opt, 'sort_order' => $i + 1]
            );
        }

        $q3 = VotingQuestion::firstOrCreate(
            ['campaign_id' => $campaign->id, 'title' => 'هل تؤيد إقامة بطولة ودية بين الأندية؟'],
            [
                'type' => 'radio',
                'is_required' => false,
                'sort_order' => 3,
            ]
        );

        VotingQuestionOption::firstOrCreate(
            ['question_id' => $q3->id, 'value' => 'yes'],
            ['label' => 'نعم', 'sort_order' => 1]
        );
        VotingQuestionOption::firstOrCreate(
            ['question_id' => $q3->id, 'value' => 'no'],
            ['label' => 'لا', 'sort_order' => 2]
        );

        // Create campaign targets (all clubs)
        foreach (Club::all() as $club) {
            VotingCampaignTarget::firstOrCreate([
                'campaign_id' => $campaign->id,
                'club_id' => $club->id,
            ]);
        }

        // Create assignments for club admins
        $clubAdmins = User::role('club-admin')->get();
        foreach ($clubAdmins as $admin) {
            CampaignAssignment::firstOrCreate(
                ['campaign_id' => $campaign->id, 'admin_id' => $admin->id],
                [
                    'club_id' => $admin->club_id,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'delivered_at' => now(),
                ]
            );
        }
    }
}
