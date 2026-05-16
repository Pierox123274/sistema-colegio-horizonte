<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAdaptiveQuestionBankController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', QuestionBank::class);

        $questions = QuestionBank::query()
            ->with('subject:id,name')
            ->orderByDesc('id')
            ->paginate(25)
            ->through(fn (QuestionBank $q) => [
                'id' => $q->id,
                'topic' => $q->topic,
                'question_type' => $q->question_type->value,
                'difficulty' => $q->difficulty->value,
                'is_active' => $q->is_active,
                'subject' => $q->subject ? ['name' => $q->subject->name] : null,
            ]);

        return Inertia::render('Intranet/Adaptive/Questions/Index', [
            'questions' => $questions,
        ]);
    }
}
