<?php

namespace Tests\Feature\Mail;

use App\Models\Enterprise\EnterpriseRequest;
use App\Models\Mail\MailSender;
use App\Models\User\User;
use AIGenerate\Services\Brevo\BrevoEnterpriseRequestService;
use AIGenerate\Services\Mails\Brevo\BrevoService;
use Tests\TestCase;

class MailTest extends TestCase
{
    public function test_send_mail_is_successful(): void
    {
        $service = app()->make(BrevoService::class);
        $template = $service->getTemplate(42);
        $sender = MailSender::first();
        $user = User::find(3);
        $service->send(template: $template, from: $sender, to: $user, mailable: $user);
        $this->assertTrue(true);
    }

    public function test_create_contract_is_successful(): void
    {
        $service = app()->make(BrevoEnterpriseRequestService::class);
        $request = EnterpriseRequest::first();
        $service->createContact($request->email, [
            'firstname'    => $request->firstname,
            'lastname'     => $request->name,
            'company'      => $request->company,
            'company_size' => $request->company_size,
            'job'          => $request->job,
            'job_title'    => $request->job_function,
        ], [15]);
        $this->assertTrue(true);
    }

}
