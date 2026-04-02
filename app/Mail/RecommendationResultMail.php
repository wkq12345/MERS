<?php

namespace App\Mail;

use App\Models\RecommendationRun;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecommendationResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecommendationRun $run,
        public array $criteriaNameMap,
        public string $senderName
    ) {}

    public function envelope(): Envelope
    {
        $methodName = optional($this->run->weightingMethod)->name ?? 'Unknown Method';

        return new Envelope(
            subject: 'MERS Recommendation Result – ' . $methodName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recommendation_result',
        );
    }
}
