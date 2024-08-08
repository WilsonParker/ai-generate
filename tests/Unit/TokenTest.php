<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $prompt = "Many words map to one token, but some don't: indivisible. Unicode characters like emojis may be split into many tokens containing the underlying bytes: 🤚🏾 Sequences of characters commonly found next to each other may be grouped together: 1234567890";
        $token_array = gpt_encode($prompt);
        $original_text = gpt_decode($token_array);

        $this->assertEquals($prompt, $original_text);
        $this->assertEquals(58, count($token_array));
    }
}
