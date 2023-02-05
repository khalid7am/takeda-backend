<?php

namespace App\Http\Controllers;

use App\Models\Answers;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionChoice;
use App\Http\Requests\AnswerQuestionRequest;
use App\Http\Resources\AnsweredQuestionResource;

class AnswersController extends Controller
{
    public function answer(AnswerQuestionRequest $request)
    {
        $question = Question::where('uuid', $request->get('question_uuid'))->first();
        $choice = QuestionChoice::where('uuid', $request->get('choice_uuid'))->first();

        $answers = Answers::create([
            'question_id' => $question->getKey(),
            'choice_id' => $choice->getKey(),
            'user_id' => auth()->id(),
            'is_correct' => $choice->is_correct,
        ]);

        return AnsweredQuestionResource::make($answers);
    }
}
