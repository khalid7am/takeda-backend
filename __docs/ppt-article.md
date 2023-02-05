# ARTICLE AS PPT

## What's on FIGMA design & GitLab issue

### Phase #1

1. User uploads a PPT file
1. Save file name to a local variable (vuex / browser.localStorage, anything)
1. Redirect to the article create page
1. PPT slides appear here

### Phase #2

1. Get the converted PPT file back (with the help of uploaded file name)
1. Creating the article
1. Add questions & mark the articles which after the question will come
    - Question will come after marked article
1. Send article to store 
    - with pptName 
    - with questionsArray
1. Same as any other article upload



## What was implemented

### Store PPT to the article as slides

New Api route added:

```php
Route::post('create/ppt', [\App\Http\Controllers\ArticleController::class, 'storePpt'])->name('create.ppt');
```
This could be a heavy duty task.
It is not queued, user should wait for the task to finish, because he/she needs the converted slides back in the next screen.

Only PPT & PPTX file types are allowed.
We can extend of course, but during the development phase, libreoffice handled these 2 perfectly.

Also extended ```\App\Http\Controllers\ArticleController -> 'store' method```

With the following:
```php
$pptName = $request->get('pptName');
$questionsArray = $request->get('questionsArray');
if ($questionsArray && $pptName) {
    $articleAsSlide = (new ArticlePptHelper)->saveMultipleArticleQuestionAsNewSlide($questionsArray, $pptName, $article->id, auth()->id());
}
```

### How Article Question save works - Phase #2

1. need a questions array
    - key = after slide number
    - value = Question in text format

1. the method will do the magic
    - make the array descending order (so the first manipulation won't affect the others)
    - create an image from the question
    - set the proper orders (increment the question after slides & give the proper order for question also)
    - save everything to database
    - attach them to article also (so article can)
        
        
```php
    $questionsArray = [
        3 => 'Which statement about lung disease related to AATD is true?',
        11 => 'Cheesecake croissant gummies donut wafer tiramisu candy canes pie biscuit. Halvah candy toffee lollipop dessert halvah sweet. Lemon drops marzipan bonbon cake gummies muffin.'
    ];
```
