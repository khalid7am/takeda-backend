<?php

namespace App\Services;

use App\Models\RelevancePoint;
use App\Models\User;

class RankingService
{
    public function getNormalUserPosition($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        // INIT DATAS
        $normalUsers =  User::query()
            ->select('id', 'role_id')
            ->normals()
            ->get()
            ->sortByDesc('user_performance_points')
            ->keyBy('id');


        $usersCount = $normalUsers->count();
        $place = 1;

        // GET CURRENT PLACE/POSITION
        foreach ($normalUsers as $key => $normalUser) {
            if ($normalUser->id == $userId) {
                break;
            }
            $place++;
        }
        if ($place >= $usersCount) {
            $place = $usersCount;
        }
        // CALCULATE AS A PERCENTAGE
        if ($usersCount > 0) {
            $placePercentage = ($place / $usersCount) * 100;
            $placePercentage = round($placePercentage, 2);
        } else {
            $placePercentage = 0;
        }

        $data = [
            'place' => $place,
            'usersCount' => $usersCount,
            'placePercentage' => $placePercentage,
            'overtTookedUsers' => $usersCount - $place,
        ];

        return $data;
    }

    public function getNormalUserPreferenceInfos($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        $usersRelevanceData = [];


        foreach (RelevancePoint::userId($userId)->with('preference.articles.questions.answers')->cursor() as $key => $usersRelevancePoint) {
            // RESET FOR EVERY ITERATION
            $place = 1;
            $point = 0;
            $answerRatio = 0;
            $correctAnswerCount = 0;
            $inCorrectAnswerCount = 0;
            $point = '';
            $uuid = '';
            $name = '';
            $image = '';
            $slug = '';

            // GET THE GOOD ANSWER QUIZ RATIO
            $preference = $usersRelevancePoint->preference;


            $uuid = $preference->uuid;
            $name = $preference->name;
            //$image = Storage::disk('public')->url($preference->image);
            // URL COME FROM STORE, THEN URL SHOULD GO BACK ALSO
            $image = $preference->image;
            $slug = $preference->slug;


            // YES, I KNOW IT LOOKS LIKE HEAVY, BUT THE RELATIONS ARE LOADED
            // BELOW WILL BE JUST A FEW FOREACH

            // ANSWER -> QUESTION -> ARTICLE -> preference
            // PREFERENCE -> ARTICLE S -> QUESTIONS -> ANSWERS
            // PREFERENCE -> GET EVERY ARTICLE -> GET EVERY QUESTION -> GET EVERY ANSWER ( CALC GOOD AND BAD )
            foreach ($preference->articles as $key => $article) {
                foreach ($article->questions  as $key => $question) {
                    foreach ($question->answers as $key => $answer) {
                        if ($answer->is_correct) {
                            $correctAnswerCount += 1;
                        } else {
                            $inCorrectAnswerCount += 1;
                        }
                    }
                }
            }

            // GET THE POINT AND POSITION
            //$exactPreferenceRows = $everyRelevancePoints->where('preference_id', $preference->id)->sortByDesc('point');
            //dd(RelevancePoint::preferenceId($preference->id)->orderBy('point', 'desc')->count());
            // foreach (RelevancePoint::preferenceId($preference->id)->orderBy('point', 'desc')->cursor() as $key => $exactPreferenceRow) {
            //     if ($exactPreferenceRow->user_id == $userId) {
            //         $point = $exactPreferenceRow->point;
            //         break;
            //     }
            //     $place++;
            // }

            $relevanceUsers = RelevancePoint::query()
                ->preferenceId($preference->id)
                ->orderBy('point', 'desc')
                ->groupBy('user_id')
                ->get();

            //dd($relevanceUsers);

            foreach ($relevanceUsers as $key => $relevanceUser) {
                if ($relevanceUser->user_id == $userId) {
                    $point = $relevanceUser->point;
                    break;
                }
                $place++;
            }

            $answerRatio = 100;
            // CHECK IF IT IS BIGGER THAN 0
            // 0 DIVISION _NOT_ WANTED
            if (($correctAnswerCount + $inCorrectAnswerCount) > 0) {
                $answerRatio = $correctAnswerCount / ($correctAnswerCount + $inCorrectAnswerCount) * 100;
                $answerRatio = round($answerRatio, 2);
            }

            // FILL THE ARRAY WITH SO MUCH VALUED DATA
            $usersRelevanceData[$preference->id] = [
                'preference_uuid' => $uuid,
                'preferences_name' => $name,
                'preferences_image' => config('app.url') . \Storage::url($image),
                'preferences_slug' => $slug,
                'usersCount' => $relevanceUsers->count(),
                'position' => $place,
                'point' => $point,
                'answerRatio' => $answerRatio,
                'correctAnswerCount' => $correctAnswerCount,
                'inCorrectAnswerCount' => $inCorrectAnswerCount,
            ];
        }

        return $usersRelevanceData;
    }

    public function getNormalUserRanking($userId = null)
    {
        $placePercentage = $this->getNormalUserPosition($userId)['placePercentage'];

        return round($placePercentage, 2);
    }

    public function getAuthorUserPosition($userId = null)
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        // GET AUTHORS WITH ARTICLES COUNT
        $authors = User::query()
            //->authors()
            ->withCount(['articles' => function ($query) {
                // COUNT ARTICLES IF THEY ARE PUBLISHED
                $query->whereNotNull('published_at');
            }])
            ->whereHas('articles', function ($query) {
                // COUNT ARTICLES IF THEY ARE PUBLISHED
                $query->whereNotNull('published_at');
            })
            ->get()
            ->sortByDesc('articles_count')
            ->keyBy('id');



        $usersCount = $authors->count();
        $place = 1;

        // GET CURRENT PLACE/POSITION
        foreach ($authors as $key => $author) {
            if ($author->id == $userId) {
                break;
            }
            $place++;
        }

        // CALCULATE AS A PERCENTAGE

        if ($place >= $usersCount) {
            $place = $usersCount;
        }
        // CALCULATE AS A PERCENTAGE
        if ($usersCount > 0) {
            $placePercentage = ($place / $usersCount) * 100;
            $placePercentage = round($placePercentage, 2);
        } else {
            $placePercentage = 0;
        }

        //dd($placePercentage);
        $data = [
            'place' => $place,
            'usersCount' => $usersCount,
            'placePercentage' => $placePercentage,
            'overtTookedUsers' => $usersCount - $place,
        ];

        return $data;
    }

    public function getAuthorUserRanking($userId = null)
    {
        $placePercentage = $this->getAuthorUserPosition($userId)['placePercentage'];

        return round($placePercentage, 2);
    }

    public function getUserDynamicRankingValues($user)
    {
        // INIT
        $attributes = collect([]);
        // GET THE INFORMATIONS FROM THE CONFIG FILE
        $userDynamicRanks = config('takeda-ranking.user_dynamic');

        // LOOP THROUGH THE CONFIG
        foreach ($userDynamicRanks as $key => $userDynamicRank) {

            // CHECK IF USER HAS THE DESIRED POINT WHICH IS DEFINED

            if ($user->user_current_position_percentage <= $key) {
                // IF USER HAS ENOUGH POINT, HE WILL GET THE ATTRIBUTES
                $attributes->push($userDynamicRank);
                // BREAK THE LOOP SO IT WON'T ADD THE LOWER LEVELS TO THIS COLLECTION
                break;
            } else {
                continue;
            }
        }

        // GET THE "FIRST" ELEMENT
        // SO THE RESULT WILL CONTAIN ONLY THE KEY-VALUE PAIRS
        return $attributes->first();
    }

    public function getAuthorDynamicRankingValues($user)
    {
        // INIT
        $attributes = collect([]);
        // GET THE INFORMATIONS FROM THE CONFIG FILE
        $authorDynamicRanks = config('takeda-ranking.author_dynamic');

        // LOOP THROUGH THE CONFIG
        foreach ($authorDynamicRanks as $key => $authorDynamicRank) {

            // CHECK IF USER HAS THE DESIRED POINT WHICH IS DEFINED

            if ($user->author_current_position_percentage <= $key) {
                // IF USER HAS ENOUGH POINT, HE WILL GET THE ATTRIBUTES
                $attributes->push($authorDynamicRank);
                // BREAK THE LOOP SO IT WON'T ADD THE LOWER LEVELS TO THIS COLLECTION
                break;
            } else {
                continue;
            }
        }

        // GET THE "FIRST" ELEMENT
        // SO THE RESULT WILL CONTAIN ONLY THE KEY-VALUE PAIRS
        return $attributes->first();
    }

    public function getStaticRankingValues($user)
    {
        // INIT
        $attributes = collect([]);
        // GET THE INFORMATIONS FROM THE CONFIG FILE
        $staticRanks = config('takeda-ranking.static');

        // LOOP THROUGH THE CONFIG
        foreach ($staticRanks as $key => $staticRank) {
            // CHECK IF USER HAS THE DESIRED POINT WHICH IS DEFINED
            if ($key <= $user->getUserPerformancePoints()) {
                // IF USER HAS ENOUGH POINT, HE WILL GET THE ATTRIBUTES
                $attributes->push($staticRank);
                // BREAK THE LOOP SO IT WON'T ADD THE LOWER LEVELS TO THIS COLLECTION
                break;
            }
        }

        // GET THE "FIRST" ELEMENT
        // SO THE RESULT WILL CONTAIN ONLY THE KEY-VALUE PAIRS
        return $attributes->first();
    }

    public function getNextStaticRankingPoint($user)
    {
        // INIT
        $pointToNext = 0;
        $userPoints = $user->getUserPerformancePoints();

        // GET THE INFORMATIONS FROM THE CONFIG FILE
        $staticRanks = config('takeda-ranking.static');

        $reverseStaticRanks = ksort($staticRanks);
        // LOOP THROUGH THE CONFIG
        foreach ($staticRanks as $key => $staticRank) {
            if ($userPoints < $key) {
                $pointToNext = $key - $userPoints;
                break;
            } else {
                continue;
            }
        }

        // GET THE "FIRST" ELEMENT
        // SO THE RESULT WILL CONTAIN ONLY THE KEY-VALUE PAIRS
        return $pointToNext;
    }

    public function getIconByRank($color, $rank)
    {
        $iconsBySizes = [];
        $sizes = [
            30,
            40,
            105,
            438
        ];

        foreach ($sizes as $size) {
            $iconsBySizes[$size] = [
                'png' => asset(config('takeda-ranking.icon_path').'/' . $size .'/png/'. $color.'_'.$rank.'.png'),
                'svg' => asset(config('takeda-ranking.icon_path').'/' . $size.'/svg/'. $color.'_'.$rank.'.svg'),
            ];
        }


        return $iconsBySizes;
    }
}
