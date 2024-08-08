<?php
$imageRoot = env('IMAGE_ROOT_URL', 'https://image.ai_generate.com');

return [
    'env'     => env('APP_ENV', 'test'),
    'sitemap' => env('APP_SITEMAP_URL', ''),
    'images'  => [
        'root'    => $imageRoot,
        'default' => $imageRoot . '/' . env('DEFAULT_IMAGE', 'default.png'),
        'avatar'  => $imageRoot . '/' . env('DEFAULT_AVATAR_IMAGE', 'default.png'),
    ],
    'prompt'  => [
        'generate' => [
            'free_limit' => env('PROMPT_FREE_GENERATE_LIMIT', 3),
        ],
    ],
];
