<?php

namespace Tests\Feature\OpenAI;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class OpenAITest extends TestCase
{

    public function test_call_image_open_ai_using_library(): void
    {
        $result = OpenAI::images()->create([
            // A text description of the desired image(s). The maximum length is 1000 characters.
            'prompt' => 'A cute baby sea otter',
            // The number of images to generate. Must be between 1 and 10.
            'n' => 1,
            // The size of the generated images. Must be one of 256x256, 512x512, or 1024x1024.
            'size' => '256x256',
            // The format in which the generated images are returned. Must be one of url or b64_json.
            // 'response_format' => 'url',
            // A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse
            // 'user' => '',
        ]);

//        dump($result);
        $this->assertIsString($result->data[0]->url);
    }

    public function test_call_chat_open_ai_using_library(): void
    {
        $result = OpenAI::chat()->create([
            // ID of the model to use. See the model endpoint compatibility table for details on which models work with the Chat API.
            'model' => 'gpt-3.5-turbo',
            // The messages to generate chat completions for, in the chat format.
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => 'Who won the world series in 2020?'
                ],
                [
                    'role' => 'assistant',
                    'content' => 'The Los Angeles Dodgers won the World Series in 2020.'
                ],
                [
                    'role' => 'user',
                    'content' => 'Where was it played?'
                ],
            ],
        ]);

//        dump($result->toArray());
        $this->assertNotNull($result->choices[0]->message->content);
    }

    public function test_call_chat_open_ai_fake(): void
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'awesome!'
                        ],
                    ],
                ],
            ]),
        ]);

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'PHP is'
                ],
            ],
        ]);

        $this->assertEquals('awesome!', $result->choices[0]->message->content);
    }
}
