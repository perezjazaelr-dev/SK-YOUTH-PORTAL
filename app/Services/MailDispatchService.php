<?php

namespace App\Services;

use App\Mail\RequestReceivedMail;
use App\Mail\StatusChangedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailDispatchService
{
    /**
     * Queue a request-received email. Logs SMTP failures without interrupting the caller.
     */
    public function queueRequestReceived(object $requestModel): void
    {
        if (empty($requestModel->email)) {
            return;
        }

        try {
            Mail::to($requestModel->email)->queue(new RequestReceivedMail($requestModel));
        } catch (\Throwable $e) {
            Log::error('Failed to queue request received email.', [
                'email' => $requestModel->email,
                'model' => get_class($requestModel),
                'model_id' => $requestModel->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue a status-changed email. Logs SMTP failures without interrupting the caller.
     */
    public function queueStatusChanged(object $requestModel): void
    {
        if (empty($requestModel->email)) {
            return;
        }

        try {
            Mail::to($requestModel->email)->queue(new StatusChangedMail($requestModel));
        } catch (\Throwable $e) {
            Log::error('Failed to queue status changed email.', [
                'email' => $requestModel->email,
                'model' => get_class($requestModel),
                'model_id' => $requestModel->id ?? null,
                'status' => $requestModel->status ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
