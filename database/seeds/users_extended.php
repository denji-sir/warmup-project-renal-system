<?php

/**
 * Расширенный список пользователей для демонстрации системы
 * Больше ролей и реалистичных профилей
 */

return [
    // Дополнительные риелторы
    [
        'username' => 'sergey.realty',
        'email' => 'sergey.volkov@luxrealty.ru',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'realtor',
        'first_name' => 'Сергей',
        'last_name' => 'Волков',
        'phone' => '+7 (495) 777-88-99',
        'bio' => 'Эксперт по коммерческой недвижимости. Более 15 лет опыта в сфере инвестиций и крупных сделок.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'specialization' => 'commercial',
            'languages' => ['ru', 'en'],
            'working_hours' => '09:00-22:00',
            'min_deal_amount' => 5000000
        ])
    ],

    [
        'username' => 'anna.newbuild',
        'email' => 'anna.komarova@newbuild.moscow',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'realtor',
        'first_name' => 'Анна',
        'last_name' => 'Комарова',
        'phone' => '+7 (495) 333-22-11',
        'bio' => 'Специалист по новостройкам и первичному рынку. Помогу выбрать лучший вариант от застройщика с максимальными скидками.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'specialization' => 'new_buildings',
            'working_hours' => '10:00-19:00',
            'partner_developers' => ['ПИК', 'Самолет', 'МР Групп']
        ])
    ],

    [
        'username' => 'dmitry.suburb',
        'email' => 'dmitry.petrov@suburbanlife.ru',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'realtor',
        'first_name' => 'Дмитрий',
        'last_name' => 'Петров',
        'phone' => '+7 (495) 444-55-66',
        'bio' => 'Загородная недвижимость - моя страсть! Коттеджи, дачи, земельные участки по всему Подмосковью.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'specialization' => 'suburban',
            'coverage_area' => ['Подмосковье', 'Калужская область', 'Тульская область'],
            'working_hours' => '08:00-20:00'
        ])
    ],

    // Разнообразные арендаторы/покупатели
    [
        'username' => 'elena.family',
        'email' => 'elena.smirnova@family.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Елена',
        'last_name' => 'Смирнова',
        'phone' => '+7 (903) 555-77-99',
        'bio' => 'Ищем 3-4 комнатную квартиру для семьи с двумя детьми. Важны хорошие школы и детские сады поблизости.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'family_size' => 4,
            'children_ages' => [7, 12],
            'required_rooms' => '3-4',
            'max_budget' => 20000000,
            'important_factors' => ['schools', 'kindergartens', 'playgrounds']
        ])
    ],

    [
        'username' => 'pavel.investor',
        'email' => 'pavel.business@invest.capital',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Павел',
        'last_name' => 'Инвестор',
        'phone' => '+7 (495) 123-45-67',
        'bio' => 'Профессиональный инвестор. Рассматриваю недвижимость для инвестиций и коммерческие проекты.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'investment_budget' => 50000000,
            'roi_target' => 15,
            'property_types' => ['commercial', 'new_buildings', 'land'],
            'investment_horizon' => '3-5 years'
        ])
    ],

    [
        'username' => 'olga.startup',
        'email' => 'olga.tech@startup.io',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Ольга',
        'last_name' => 'Технова',
        'phone' => '+7 (926) 888-99-00',
        'bio' => 'IT-предприниматель. Ищу современный офис для tech-стартапа в центре Москвы.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'business_type' => 'IT',
            'team_size' => 15,
            'required_features' => ['high_speed_internet', 'meeting_rooms', 'kitchen'],
            'location_preference' => 'city_center'
        ])
    ],

    [
        'username' => 'mikhail.retired',
        'email' => 'mikhail.pensioner@mail.ru',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Михаил',
        'last_name' => 'Пенсионеров',
        'phone' => '+7 (916) 222-33-44',
        'bio' => 'Пенсионер, хочу переехать в тихое место поближе к природе. Рассматриваю дачу или дом в пригороде.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'age' => 65,
            'lifestyle' => 'quiet',
            'preferences' => ['nature', 'garden', 'low_crime'],
            'budget' => 5000000,
            'location' => 'suburban'
        ])
    ],

    [
        'username' => 'julia.student',
        'email' => 'julia.studentka@student.msu.ru',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Юлия',
        'last_name' => 'Студенткина',
        'phone' => '+7 (915) 111-22-33',
        'bio' => 'Студентка 3 курса экономического факультета МГУ. Ищу недорогое жилье рядом с университетом.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'university' => 'МГУ',
            'course' => 3,
            'max_rent' => 40000,
            'roommates_ok' => true,
            'transport_time_max' => 30
        ])
    ],

    // Дополнительный администратор
    [
        'username' => 'moderator',
        'email' => 'moderator@realestate.local',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'first_name' => 'Модератор',
        'last_name' => 'Системный',
        'phone' => '+7 (499) 000-00-00',
        'bio' => 'Модератор контента и технический администратор системы.',
        'is_active' => 1,
        'email_verified_at' => date('Y-m-d H:i:s'),
        'preferences' => json_encode([
            'role' => 'content_moderator',
            'permissions' => ['moderate_properties', 'manage_users', 'view_analytics'],
            'notifications' => ['new_properties', 'user_reports', 'system_alerts']
        ])
    ],

    // Неактивный пользователь для тестирования
    [
        'username' => 'inactive.user',
        'email' => 'inactive@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'tenant',
        'first_name' => 'Неактивный',
        'last_name' => 'Пользователь',
        'phone' => '+7 (900) 000-00-00',
        'bio' => 'Тестовый неактивный аккаунт.',
        'is_active' => 0,
        'login_attempts' => 5,
        'locked_until' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        'preferences' => json_encode([
            'status' => 'blocked_for_testing'
        ])
    ]
];