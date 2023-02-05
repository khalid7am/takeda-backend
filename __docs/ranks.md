# Give ranks to users based on performance

## User Ranks

Simply call the users generated attribute:

```php
// This shows the total performance point
$user->performance_points

// Maybe we should extend this, so make it filtereable by week/month etc.

```

```php
// This shows the current position as overall percentage
$user->user_current_position_percentage

// BEWARE
// This is calculated by performance_points, which is an overall SUM of points.
```


```php
// This shows the dynamically changeable position name.
$user->dynamic_position_name

// BEWARE
// This is calculated by current_position_percentage, which is calculated by performance_points, which is an overall SUM of points.

```


## Get icons

Simply call the user's ```rank_icons``` attribute.

Example snippet
```php 

//$user = auth()->user();
$user = User::inRandomOrder()->first();

foreach ($user->rank_icons as $size => $icons) {
    foreach ($icons as $type => $url) {
        echo '<img src="'. $url.'>';
        echo '<br>';
    }
}

```

Example response 

```php
array:4 [▼
  30 => array:2 [▼
    "png" => "http://takeda.test/assets/rank-icons/30/png/200_user_40.png"
    "svg" => "http://takeda.test/assets/rank-icons/30/svg/200_user_40.svg"
  ]
  40 => array:2 [▼
    "png" => "http://takeda.test/assets/rank-icons/40/png/200_user_40.png"
    "svg" => "http://takeda.test/assets/rank-icons/40/svg/200_user_40.svg"
  ]
  105 => array:2 [▼
    "png" => "http://takeda.test/assets/rank-icons/105/png/200_user_40.png"
    "svg" => "http://takeda.test/assets/rank-icons/105/svg/200_user_40.svg"
  ]
  438 => array:2 [▼
    "png" => "http://takeda.test/assets/rank-icons/438/png/200_user_40.png"
    "svg" => "http://takeda.test/assets/rank-icons/438/svg/200_user_40.svg"
  ]
]
```

So you can choose in which size you want to display the icon.
Also you have the ability to select the filetype.

If you know what you really need, just use the array keys to retrieve the link:

```php
//$user = auth()->user();
$user = User::inRandomOrder()->first();
$user->rank_icons['105']['png'];
```


## Settings

You can find the settings at ```config/takeda-ranking.php```

Attention!
Order matters!




## Given

There are 2 metrics:

Dynamic
Static
There are 2 types:

Normal user
Author user
If you need you can download the assets from this link, but I think it is also available in the code: https://drive.google.com/open?id=1bb4-8OSi4QFTGnSjx9h0ovWnFiM4PUI4&authuser=thetravlrd%40gmail.com&usp=drive_fs

Dynamic: This is changing. It gives the user the symbols.

Normal user ranks:

Cseresznyevirág (if the user is in the top 40%) (name: Do-ki)
Taiko dobon szereplő jel (if the user is in the top 60%) (name: Taiko-zott)
Kokeshi baba (if the user is in the top 80%) (Koke-shi-keres)
Japán makákó (if the user is in the top 90%) (Prof. Makákó)
Gésa (in case the user is a woman), and Showgun (in case the user is a man) (if the user is in the top 95%) (name (woman): Geishampion; name (man): Showgun)
Author ranks:

Japán daru (if the user is in the top 40%) (name: Tollforgató daru)
Bonsai (if the user is in the top 80%) (name: Japán kert-ész)
Fekete öv (if the user is in the top 95%) (name: Sensei science)
If the user is an author he will be ranked in the author categories automatically. A normal user can only achieve normal user ranks.

These ranks are changing continuously. The parameters for this are provided above. The top x% means that the users have to be at that level in the leaderboard of performance points.

These are also having names, which should show up in different places at the site, providing some sort of a nickname for the user.

Static

These ranks are given if the user achieves a certain number of performance points.

These ranks are:

Bambusz (homokszín) (after 0 points)
Gyöngy (rózsaszínes) (after 20 points)
Jáde (világos zöld) (after 80 points)
Brilliáns (világoskék) (after 200 points)
These are giving the background color of the symbols. Also giving a naming as the previous one. So a nickname can look something like: Bambusz Geishampion.
