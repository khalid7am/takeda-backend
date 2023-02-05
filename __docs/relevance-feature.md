
# Save relevance

### Basics
If anybody gives 1 good answer for 1 article
    - save it, that the article (it's tags/categories/preferences) is relevant for the user
    - every user will have a relevance point for every ***tags/categories/preferences***
    - submitting same article will not increase relevance points, this will be handled by frontend

### Store relevance

Use the following snippet anywhere.
Need only 2 ID
- user ID
- article ID

The rest will be handled! :)

```php
(new App\Helpers\Relevance\RelevanceStoreHelper)->start()
        ->userId($userId)
        ->articleId($articleId)
        ->send();
```

### How it works
- Get the preferences/tags of article.
- This method will find or create relevance & increase the relevance point by 1.
- Automatically start with 0, so the firstOrCreate method will create with 0,
immediately after that, the point will be increased by 1.


# Save already (correctly) answered question to user

### Why this needed?

- The relevance search is based on this thesis:
    - show the relevance & NOT answered (by the user) articles 
- To use the user-answer-question-article roadmap, this would take too much time & compute
- With the help of this method, it will be a single query from a single table.

### How it was implemented

- Made a new model with migration
- Made a new StoreHelper
- This store helper is also added to the ```Answer``` model's boot method
- Don't have to call anywhere, it will automatically store the information needed.


```php
public static function boot()
    {
        parent::boot();

        static::created(function ($answer) {
            // IF THE ANSWER IS CORRECT
            // RUN THE HELPER TO STORE THE ARTICLE-USER
            if ($answer->is_correct) {
                $articleCorrectAnswerUser = (new ArticleAnsweredCorrectUserStoreHelper)
                    ->start()
                    ->userId($answer->user_id)
                    ->articleId($answer->question->article_id)
                    ->send();
            }
        });
    }
```


# Search

    - mainly focus on the relevance points
    - more relevant the article, get higher (get lower number) at search result
    - article - sum of relevance points is highest -> lowest
        - currently this is work as discussed
        - highest preference relevance point will give the order of the articles
        - we can implement the desired method, but it will need more time & logic
    - not submitted with answer
	

### How to use it

Need the following:
    - user Id 
    - search term
    - the whole paginate part is optional

Use the following snippet:

```php
    $results = (new RelevanceSearchHelper)->start()
        ->userId($userId)
        ->searchFor($searchTerm)
        ->paginate($paginateAmount)
        ->get();
```

Paginate is just optional, if not provided, default ```$amount = 12```
```php
->paginate($amount)
```

Without paginate part:

```php
    $results = (new RelevanceSearchHelper)->start()
        ->userId($userId)
        ->searchFor($searchTerm)
        ->get();
```





### Side note

relevance_points -> can be deleted, or refactored

### Dictionary:

Article
Preference = Category = Tag

Article (n-n) Preference




------------------------------------
2022-05-30 - Meeting

Add new table 
    -> user / article
        -> here we can store 1 good answered article

- mennyi találatot kellene kiadni a végül a keresőnek?
    - paginate (parameter = amount)
- mi alapján tudhatom, hogy egy adott cikkhez már történt legalább 1 jó válaszadás?
    - létrehozhatok segéd táblát + segéd helpert a könnyed tárolásra

 mysql json
    - regexp

if relevance null -> everything = 0

Todos
 - [x] paginate result
 - [x] if no relevance points, show result also
 - [x] article -> content -> mysql regexp show result
 - [x] new table to store article_correct_answered_user
 - [x] new method to easily & automatically store article_correct_answered_user
