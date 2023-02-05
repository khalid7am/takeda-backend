<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Question;
use Illuminate\Support\Str;

class QuestionService
{
    public static function createFromRequestArray(Article $article, array $data)
    {
        $existingQuestions = $article->questions;
        // $questions = collect([]);

        $article->questions()->delete();
        
        $order = 0;

        foreach ($data as $key => $questionData) {
            $question = null;
            if (!empty($questionData['uuid']) && $existingQuestions->where('uuid', $questionData['uuid'])) {
                $question = $existingQuestions->where('uuid', $questionData['uuid']);
                $question->update([
                    'question' => $questionData['question'],
                    'show_at' => $questionData['questionTime'] ?? null,
                    'order' => $order++,
                ]);

                self::storeChoices($question, $questionData, true);
                if (!empty($questionData['explantionText'])) {
                    self::storeDescriptions($question, $questionData['explantionText'], true);
                }
                if (!empty($questionData['mediaImage'])) {
                    self::storeImages($question, $questionData['mediaImage'], true, $questionData['changed']);
                }
            } else {
                $question = $article->questions()->create([
                    'question' => $questionData['question'],
                    'paragraph_uuid' => $questionData['paragraphUuid'] ?? null,
                    'show_at' => $questionData['questionTime'] ? (int)$questionData['questionTime'] : null,
                    'order' => $order++,
                ]);

                self::storeChoices($question, $questionData);
                if (!empty($questionData['explantionText'])) {
                    self::storeDescriptions($question, $questionData['explantionText']);
                }
                if (!empty($questionData['mediaImage'])) {
                    self::storeImages($question, $questionData['mediaImage']);
                }
            }

            $article->questions()->save($question);
        }
    }

    public static function storeChoices(Question $question, array $data, $update = false)
    {
        if ($update) {
            $question->choices()->delete();
        }
        foreach ($data['answers'] as $key => $answer) {
            $question->choices()->create([
                'answer' => $answer['value'],
                'is_correct' => $data['rightAnswer'] == $key,
                'order' => $key
            ]);
        }
    }

    public static function storeDescriptions(Question $question, string $data, $update = false)
    {
        if ($update) {
            if (is_null($data) || $data == '') {
                $question->reasons()->delete();
            } else {
                $question->reasons()->sync([
                    'content' => $data,
                    'order' => 0
                ]);
            }
        } else {
            $question->reasons()->create([
                'content' => $data,
                'order' => 0
            ]);
        }

    }

    public static function storeImages(Question $question, string $data, $update = false, $changed = true)
    {
        if ($update) {
            $question->images()->delete();
            if (!is_null($data)) {
                $question->images()->create([
                    'path' => $data,
                    'order' => 0,
                ]);
            }
        } else {
            $question->images()->create([
                'path' => $data,
                'order' => 0,
            ]);
        }
    }
}
