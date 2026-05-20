@extends('layouts.user')

@section('title', 'SUS Survey - MERS')

@push('styles')
    <style>
        .sus-hero {
            background: linear-gradient(135deg, #48a75b 0%, #a2b0cd 100%);
            color: white;
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 22px;
        }

        .sus-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .sus-question {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .sus-scale {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
        }

        .sus-choice {
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 4px;
            font-size: 12px;
            cursor: pointer;
            background: #fff;
            transition: background-color 0.2s ease;
        }

        .sus-choice:hover {
            background: #f9fafb;
        }

        .sus-question.has-error {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .sus-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 8px;
            display: none;
        }

        .sus-error.show {
            display: block;
        }

        .sus-form-error {
            display: none;
        }

        .sus-form-error.show {
            display: block;
        }
    </style>
@endpush

@section('content')
    @php
        $methodRouteMap = [
            'drm' => route('recommendations.drm'),
            'hdm' => route('recommendations.hdm'),
            'kano' => route('recommendations.kano'),
        ];

        $susStatements = [
            1 => 'I think that I would like to use this system frequently.',
            2 => 'I found the system unnecessarily complex.',
            3 => 'I thought the system was easy to use.',
            4 => 'I think that I would need the support of a technical person to be able to use this system.',
            5 => 'I found the various functions in this system were well integrated.',
            6 => 'I thought there was too much inconsistency in this system.',
            7 => 'I would imagine that most people would learn to use this system very quickly.',
            8 => 'I found the system very cumbersome to use.',
            9 => 'I felt very confident using the system.',
            10 => 'I needed to learn a lot of things before I could get going with this system.',
        ];

        $submissionData = $submission ?? null;
        $submissionResponses = optional($submissionData)->sus_responses;
        $submissionResponses = is_array($submissionResponses) ? $submissionResponses : [];
        $alreadySubmitted = !is_null(optional($submissionData)->submitted_at);
        $selectedMethodCode = optional($selectedMethod)->code;
        $selectedMethodName = optional($selectedMethod)->name;
        $hasMethodRun = !is_null($methodRun ?? null);
    @endphp
          <a href="{{ route('recommendations.compare') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    <div class="sus-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-clipboard2-pulse me-2"></i>System Usability Scale (SUS)</h2>
            <p class="mb-0" style="opacity:0.9;">
                @if ($selectedMethodName)
                    Share your usability feedback for <b>{{ $selectedMethodName }}</b>.
                @else
                    Choose a method to answer SUS for that specific recommendation method.
                @endif
            </p>
            <p class="small text-muted mt-2 mb-0"><b> 1 = Strongly Disagree | 2 = Disagree | 3 = Neutral | 4 = Agree | 5 =
                    Strongly Agree.</b></p>
        </div>
    </div>

    <div class="sus-card">
        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        </div>

        @if (!$selectedMethod)
            <div class="alert alert-warning small">
                Select a method to answer method-specific SUS.
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach ($methods as $method)
                    <a href="{{ route('recommendations.sus.index', ['method_code' => $method->code]) }}"
                        class="btn btn-outline-dark btn-sm">
                        {{ $method->name }} SUS
                    </a>
                @endforeach
            </div>
        @elseif (!$hasMethodRun)
            <div class="alert alert-info small">
                No completed run found for <b>{{ $selectedMethodName }}</b>. Please run this method first, then answer SUS.
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ $methodRouteMap[$selectedMethodCode] ?? route('recommendations.compare') }}"
                    class="btn btn-dark btn-sm">
                    Start {{ $selectedMethodName }}
                </a>
                <a href="{{ route('recommendations.compare') }}" class="btn btn-outline-secondary btn-sm">
                    Back to Compare
                </a>
            </div>
        @endif

        @if ($alreadySubmitted && $selectedMethod)
            <div class="alert alert-info small">
                You already submitted SUS feedback for <b>{{ $selectedMethodName }}</b>.
            </div>
        @endif

        @if ($selectedMethod && $hasMethodRun)
            <form method="POST" action="{{ route('recommendations.sus.submit') }}" id="sus-form" novalidate>
                @csrf
                <input type="hidden" name="method_code" value="{{ $selectedMethodCode }}">

                @if ($errors->any())
                    <div class="alert alert-danger sus-form-error show" id="sus-form-error" role="alert">
                        Please answer all questions before submitting the survey.
                    </div>
                @else
                    <div class="alert alert-danger sus-form-error" id="sus-form-error" role="alert">
                        Please answer all questions before submitting the survey.
                    </div>
                @endif

                @for ($i = 1; $i <= 10; $i++)
                    @php
                        $defaultAnswer = isset($submissionResponses['q' . $i])
                            ? (int) $submissionResponses['q' . $i]
                            : null;
                        $selectedAnswer = old('sus_q' . $i, $defaultAnswer);
                    @endphp
                    <div class="sus-question" data-question="sus_q{{ $i }}">
                        <p class="small fw-semibold mb-2">Q{{ $i }}. {{ $susStatements[$i] }}</p>
                        <div class="sus-scale">
                            @for ($score = 1; $score <= 5; $score++)
                                <label class="sus-choice">
                                    <input type="radio" name="sus_q{{ $i }}" value="{{ $score }}"
                                        @checked((string) $selectedAnswer === (string) $score) @disabled($alreadySubmitted)
                                        class="form-check-input mb-1">
                                    <span class="d-block">{{ $score }}</span>
                                </label>
                            @endfor
                        </div>
                        <div class="sus-error" id="error-sus_q{{ $i }}">Please select an answer for Question
                            {{ $i }}.</div>
                    </div>
                @endfor

                @unless ($alreadySubmitted)
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-dark px-4 fw-semibold d-flex align-items-center gap-2">
                            Submit SUS Feedback
                        </button>
                    </div>
                @endunless
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @unless ($alreadySubmitted)
        <script>
            (function() {
                const form = document.getElementById('sus-form');
                if (!form) {
                    return;
                }

                const formError = document.getElementById('sus-form-error');
                const questionNames = Array.from({
                    length: 10
                }, (_, index) => `sus_q${index + 1}`);

                function setQuestionState(questionName) {
                    const selected = form.querySelector(`input[name="${questionName}"]:checked`);
                    const questionCard = form.querySelector(`[data-question="${questionName}"]`);
                    const questionError = document.getElementById(`error-${questionName}`);
                    const hasValue = Boolean(selected);

                    if (questionCard) {
                        questionCard.classList.toggle('has-error', !hasValue);
                    }

                    if (questionError) {
                        questionError.classList.toggle('show', !hasValue);
                    }

                    return hasValue;
                }

                function validateAllQuestions() {
                    const allAnswered = questionNames.every(setQuestionState);

                    if (formError) {
                        formError.classList.toggle('show', !allAnswered);
                    }

                    return allAnswered;
                }

                form.addEventListener('submit', function(event) {
                    if (!validateAllQuestions()) {
                        event.preventDefault();
                        const firstError = form.querySelector('.sus-question.has-error');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });

                questionNames.forEach(function(questionName) {
                    const radios = form.querySelectorAll(`input[name="${questionName}"]`);
                    radios.forEach(function(radio) {
                        radio.addEventListener('change', function() {
                            setQuestionState(questionName);
                            if (formError && form.querySelectorAll('.sus-question.has-error')
                                .length === 0) {
                                formError.classList.remove('show');
                            }
                        });
                    });
                });
            })();
        </script>
    @endunless
@endpush
