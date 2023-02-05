<?php

namespace App\Http\Resources;

use App\Models\Answers;
use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class AnsweredQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Answers);

        return [
            'id' => $this->resource->id,
            'user' => UserListResource::make($this->resource->user),
            'choice' => QuestionChoiceResource::make($this->resource->choice),
            'question' => QuestionResource::make($this->resource->question),
            'next_question' => QuestionResource::make($this->resource->question->article->questions()->where('order', '>',$this->resource->question->order)->first()),
            'answer_images' => $this->resource->question->getImages(),
            'answer_reasons' => $this->resource->question->reasons,
            'is_correct' => $this->resource->choice->is_correct,
            'correct_choice_uuid' => $this->resource->question->choices->where('is_correct', true)->first()->uuid,
            'choices_with_count_answers' => QuestionChoiceResource::collection($this->resource->question->choices()->withCount('answers')->get()),
            'count_question_answers' => $this->resource->question->answers->count(),
        ];
    }
}
