<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VideoPackageTemplate;

class VideoPackageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Professional video production for small businesses',
                'base_price' => 50000.00,
                'video_duration' => 60,
                'number_of_videos' => 1,
                'features' => [
                    'Professional video production',
                    'Social media optimization',
                    'Basic editing & effects',
                    'HD quality output',
                    '1 revision included'
                ],
                'deliverables' => [
                    '1 HD video file (MP4)',
                    'Social media versions',
                    'Thumbnail image',
                    'Project files (optional)'
                ],
                'is_active' => true,
                'sort_order' => 1,
                'color_scheme' => 'orange'
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Advanced video production with multiple formats',
                'base_price' => 100000.00,
                'video_duration' => 90,
                'number_of_videos' => 2,
                'features' => [
                    'Advanced video production',
                    'Multiple platform formats',
                    'Professional editing & effects',
                    '4K quality output',
                    '3 revisions included',
                    'Motion graphics',
                    'Background music'
                ],
                'deliverables' => [
                    '2 HD video files (MP4)',
                    '4K master file',
                    'Social media versions',
                    'Thumbnail images',
                    'Motion graphics elements',
                    'Project files'
                ],
                'is_active' => true,
                'sort_order' => 2,
                'color_scheme' => 'purple'
            ],
            [
                'name' => 'Custom',
                'slug' => 'custom',
                'description' => 'Tailored video production for specific needs',
                'base_price' => 150000.00,
                'video_duration' => 120,
                'number_of_videos' => 3,
                'features' => [
                    'Custom video production',
                    'Advanced effects & animation',
                    'Full project management',
                    'Unlimited revisions',
                    'Cinematic quality',
                    'Custom branding',
                    'Voice-over included',
                    'Location scouting'
                ],
                'deliverables' => [
                    '3+ HD video files (MP4)',
                    '4K master files',
                    'All social media formats',
                    'Thumbnail images',
                    'Motion graphics package',
                    'Project files',
                    'Brand guidelines',
                    'Usage rights'
                ],
                'is_active' => true,
                'sort_order' => 3,
                'color_scheme' => 'green'
            ]
        ];

        foreach ($templates as $template) {
            VideoPackageTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
