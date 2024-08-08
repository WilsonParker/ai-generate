<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;

class StockGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:stock-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate stock generate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\Stock\StockGenerate::with('images')->chunk(1000, function ($stockGenerates) {
            $stockGenerates->each(function ($stockGenerate) {
                $image = $stockGenerate->images->first();
                $properties = $image->custom_properties ?? null;
                if (!$properties) {
                    return;
                }
                if (isset($properties['denoisingStrength'])) {
                    $isPoseVariation = $properties['denoisingStrength'] == 1;
                } else if (isset($properties['denoising_strength'])) {
                    $isPoseVariation = $properties['denoising_strength'] == 1;
                } else {
                    $isPoseVariation = false;
                }
                $stockGenerate->update([
                    'gender' => $properties['gender'] ?? null,
                    'age' => empty($properties['age']) ? null : (int)$properties['age'],
                    'ethnicity' => $properties['ethnicity'] ?? null,
                    'is_skin_reality' => $properties['lora'] != null && $properties['lora'] != "",
                    'is_pose_variation' => $isPoseVariation,
                ]);
            });
        });
    }
}
