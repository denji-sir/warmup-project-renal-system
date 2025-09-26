<?php

/**
 * Дополнительные данные для категорий недвижимости
 * Подкатегории и расширенные настройки
 */

return [
    // Подкатегории для квартир
    [
        'name' => 'Студии',
        'slug' => 'studios',
        'parent_id' => 1, // Квартиры
        'description' => 'Квартиры-студии и малогабаритное жилье',
        'icon' => 'home-heart',
        'color' => '#3498db',
        'sort_order' => 1
    ],
    [
        'name' => '1-комнатные',
        'slug' => '1-room',
        'parent_id' => 1,
        'description' => 'Однокомнатные квартиры',
        'icon' => 'door-closed',
        'color' => '#3498db',
        'sort_order' => 2
    ],
    [
        'name' => '2-комнатные',
        'slug' => '2-room',
        'parent_id' => 1,
        'description' => 'Двухкомнатные квартиры',
        'icon' => 'door-closed',
        'color' => '#3498db',
        'sort_order' => 3
    ],
    [
        'name' => '3-комнатные',
        'slug' => '3-room',
        'parent_id' => 1,
        'description' => 'Трехкомнатные квартиры',
        'icon' => 'door-closed',
        'color' => '#3498db',
        'sort_order' => 4
    ],
    [
        'name' => 'Многокомнатные',
        'slug' => 'multi-room',
        'parent_id' => 1,
        'description' => 'Квартиры с 4+ комнатами',
        'icon' => 'building-house',
        'color' => '#3498db',
        'sort_order' => 5
    ],
    
    // Подкатегории для домов
    [
        'name' => 'Коттеджи',
        'slug' => 'cottages',
        'parent_id' => 2, // Дома и коттеджи
        'description' => 'Загородные коттеджи',
        'icon' => 'building-house',
        'color' => '#27ae60',
        'sort_order' => 1
    ],
    [
        'name' => 'Таунхаусы',
        'slug' => 'townhouses',
        'parent_id' => 2,
        'description' => 'Таунхаусы и дуплексы',
        'icon' => 'buildings',
        'color' => '#27ae60',
        'sort_order' => 2
    ],
    [
        'name' => 'Дачи',
        'slug' => 'dachas',
        'parent_id' => 2,
        'description' => 'Дачные дома и участки',
        'icon' => 'tree',
        'color' => '#27ae60',
        'sort_order' => 3
    ],
    
    // Подкатегории для коммерческой недвижимости
    [
        'name' => 'Офисы',
        'slug' => 'offices',
        'parent_id' => 3, // Коммерческая
        'description' => 'Офисные помещения',
        'icon' => 'building-office',
        'color' => '#8e44ad',
        'sort_order' => 1
    ],
    [
        'name' => 'Торговые помещения',
        'slug' => 'retail',
        'parent_id' => 3,
        'description' => 'Магазины, торговые площади',
        'icon' => 'shop',
        'color' => '#8e44ad',
        'sort_order' => 2
    ],
    [
        'name' => 'Склады',
        'slug' => 'warehouses',
        'parent_id' => 3,
        'description' => 'Складские помещения',
        'icon' => 'package',
        'color' => '#8e44ad',
        'sort_order' => 3
    ],
    [
        'name' => 'Производство',
        'slug' => 'manufacturing',
        'parent_id' => 3,
        'description' => 'Производственные помещения',
        'icon' => 'gear',
        'color' => '#8e44ad',
        'sort_order' => 4
    ],
    
    // Подкатегории для земли
    [
        'name' => 'ИЖС',
        'slug' => 'individual-housing',
        'parent_id' => 4, // Земля
        'description' => 'Участки для индивидуального жилищного строительства',
        'icon' => 'hammer',
        'color' => '#f39c12',
        'sort_order' => 1
    ],
    [
        'name' => 'СНТ',
        'slug' => 'garden-partnerships',
        'parent_id' => 4,
        'description' => 'Садовые некоммерческие товарищества',
        'icon' => 'leaf',
        'color' => '#f39c12',
        'sort_order' => 2
    ],
    [
        'name' => 'Коммерческие участки',
        'slug' => 'commercial-land',
        'parent_id' => 4,
        'description' => 'Земля под коммерческую застройку',
        'icon' => 'building',
        'color' => '#f39c12',
        'sort_order' => 3
    ]
];