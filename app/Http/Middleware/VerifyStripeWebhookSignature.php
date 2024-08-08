<?php

namespace App\Http\Middleware;

use AIGenerate\Services\Stripe\Enums\EventTypes;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class VerifyStripeWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $payload = $request->getContent();

        try {
            Webhook::constructEvent($payload, $request->header('Stripe-Signature'), EventTypes::from(json_decode($payload)->type)->getKey());
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            throw new Exception($exception->getMessage(), 400);
        }

        return $next($request);
    }
}
