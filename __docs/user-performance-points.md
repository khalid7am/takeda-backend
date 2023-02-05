# Final decision

Article points:
Starting article (every wrong answer or 1 or every good ) = 1 point
Every good answer = 1 point

- Every tag/preferance/topic will get the *SAME* point, which the article got.
- Summarized points = sum(article points)
- need to know in which timeframe the user got how many points (monthly user/point table)


# Implementation

## Last step to do

Please check at the bottom, but read the doc first!


## Store user performance

What was given:
- For every started and not correctly answered Articles, the user will get:
    - 1 point for question
    - 1 point for every tag/topic/preference
- For every good answer for the Article questions, the user will get:
    - 1 point for question
    - 1 point for every tag/topic/preference


The above means, user cat get a maximum:
count(question) + 1 point for every Article.
Every tag/topic/preference point will help in the relevance searching.
Every question point will help to measure user performance.

### Get if the user is filled that article

```php
$userFilledThisArticle = (new App\Repositories\ArticleUserAnsweredRepository)->userFilledThisArticle($userId, $articleId);

```

The return value will be boolean, true/false.


### Call the Store Helper

Here is the raw helper:

```php
$userPerformanceStoreHelper = (new App\Helpers\Relevance\UserPerformanceStoreHelper)->start()
        ->articleId($article->id)
        ->userId($userId)
        ->send();
```

Here is a job which will call the helper:

```php
\App\Jobs\PerformanceRelevance\UserPerformanceStoreJob::dispatch($userId, $articleId);
```

The benefit of using a job, is that it can be queueable.


## Recieve the users with their SUM points

Please inspect the following snippet:

```php
    $currentMonth = Carbon\Carbon::now()->format('Y-m-d');
    $returnCount = null;
    //$returnCount = 10;
    $usersWithTheirPoints = (new App\Repositories\ArticleUserAnsweredRepository)->getExactMonthPointsByUsers($currentMonth, $returnCount);

    return $usersWithTheirPoints;
```

Return:

```json
  #items: array:3 [▼
    "Kenyatta Schneider" => 1000
    "Jettie Kuhn" => 1000
    "Enid Keebler" => 1000
  ]
```

### Steps to recieve informations: 

- You should provide the exact month in a ```Y-m-d``` format.
- Optionally you can give a maximum number of return row value. ```returnCount```
- Call the repository method.
    - first parameter is required (month)
    - second parameter is optional -> row count

- That's all.


## Recieve current user's performance


Simply call:
```php
// $user = auth()->user();
$user->overall_performancePoints
```

Reply will be the following:

```json
array:2 [▼
  "overall" => "10860"
  "current_month" => "52860"
]
```



## Last step TODO

Need to place to a desired API endpoint which the frontend will call after every answer is submitted.
The method will take care of looping through the answers.

This job just need to be called, the rest will be handled:

```php
\App\Jobs\PerformanceRelevance\UserPerformanceStoreJob::dispatch($userId, $articleId);
```
