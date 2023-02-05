<?php

return [
    'icon_path' => 'assets/rank-icons',
    //   Bambusz (homokszín) (after 0 points)
    //   Gyöngy (rózsaszínes) (after 20 points)
    //   Jáde (világos zöld) (after 80 points)
    //   Brilliáns (világoskék) (after 200 points)
    'static' => [
        200 => [
            'title' => 'Brilliáns',
            'color' => 'világoskék',
            'unique' => 200,
        ],
        80 => [
            'title' => 'Jáde',
            'color' => 'világos zöld',
            'unique' => 80,
        ],
        20 => [
            'title' => 'Gyöngy',
            'color' => 'rózsaszínes',
            'unique' => 20,
        ],
        0 => [
            'title' => 'Bambusz',
            'color' => 'homokszín',
            'unique' => 0,
        ],

    ],

    // Cseresznyevirág (if the user is in the top 40%) (name: Do-ki)
    // Taiko dobon szereplő jel (if the user is in the top 60%) (name: Taiko-zott)
    // Kokeshi baba (if the user is in the top 80%) (Koke-shi-keres)
    // Japán makákó (if the user is in the top 90%) (Prof. Makákó)
    // Gésa (in case the user is a woman), and  (if the user is in the top 95%)
    // Showgun (in case the user is a man) (if the user is in the top 95%) (name (woman): Geishampion; name (man): Showgun)
    'user_dynamic' => [
        5 => [
            'title' => 'Showgun',
            'name' => 'Showgun',
            'title_woman' => 'Gésa',
            'name_woman' => 'Geishampion',
            'unique' => 'user_95',
            'unique_woman' => 'user_95_woman',

        ],
        10 => [
            'title' => 'Japán makákó',
            'name' => 'Prof. Makákó',
            'unique' => 'user_90',
        ],
        20 => [
            'title' => 'Kokeshi baba',
            'name' => 'Koke-shi-keres',
            'unique' => 'user_80',
        ],
        40 => [
            'title' => 'Taiko dobon szereplő jel',
            'name' => 'Taiko-zott',
            'unique' => 'user_60',
        ],

        100 => [
            'title' => 'Cseresznyevirág',
            'name' => 'Do-ki',
            'unique' => 'user_40',
        ],

    ],

    // Japán daru (if the user is in the top 40%) (name: Tollforgató daru)
    // Bonsai (if the user is in the top 80%) (name: Japán kert-ész)
    // Fekete öv (if the user is in the top 95%) (name: Sensei science)

    'author_dynamic' => [

        5 => [
            'title' => 'Fekete öv',
            'name' => 'Sensei science',
            'unique' => 'editor_95',
        ],

        20 => [
            'title' => 'Bonsai',
            'name' => 'Japán kert-ész',
            'unique' => 'editor_80',
        ],

        100 => [
            'title' => 'Japán daru',
            'name' => 'Tollforgató daru',
            'unique' => 'editor_40',
        ],

    ]
];
