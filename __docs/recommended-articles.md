# Recommended articles

## API endpoint

The following line is in charge at the ```api.php``` file
```php
Route::get('recommended', [\App\Http\Controllers\ArticleController::class, 'recommended'])->name('recommended');
```

Simple call the following endpoint with a ***GET*** request:

```api/v1/articles/recommended```

Of course, you must be logged in!

