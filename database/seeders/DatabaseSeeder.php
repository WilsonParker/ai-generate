<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use AIGenerate\Models\Database\Seeders\ImageToImageTypeSeeder;
use AIGenerate\Models\Database\Seeders\PointSeeder;
use AIGenerate\Models\Database\Seeders\PointTypeSeeder;
use AIGenerate\Models\Database\Seeders\PromptCategorySeeder;
use AIGenerate\Models\Database\Seeders\PromptGenerateSeeder;
use AIGenerate\Models\Database\Seeders\PromptSeeder;
use AIGenerate\Models\Database\Seeders\PromptStatusSeeder;
use AIGenerate\Models\Database\Seeders\PromptTypeSeeder;
use AIGenerate\Models\Database\Seeders\StripeSetSeeder;
use AIGenerate\Models\Database\Seeders\StripeStatusSeeder;
use AIGenerate\Models\Database\Seeders\StripeWebhookTypeSeeder;
use AIGenerate\Models\Database\Seeders\TextToImageTypeSeeder;
use AIGenerate\Models\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StripeWebhookTypeSeeder::class,
            StripeStatusSeeder::class,
            UserSeeder::class,
            PromptTypeSeeder::class,
            PromptStatusSeeder::class,
            PromptSeeder::class,
            PromptCategorySeeder::class,
            PromptGenerateSeeder::class,
            PointTypeSeeder::class,
            PointSeeder::class,
            StripeSetSeeder::class,
            StockStatusSeeder::class,
            TextToImageTypeSeeder::class,
            ImageToImageTypeSeeder::class,
        ]);
    }
}
