<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Request;
use Exception;

class HomeController extends Controller
{
    /**
     * Display dashboard for authenticated users
     */
    public function dashboard()
    {
        // Check if user is authenticated
        $auth = new \Core\Auth();
        if (!$auth->check()) {
            header('Location: /login');
            exit;
        }

        $user = $auth->user();
        
        return $this->view('dashboard', [
            'user' => $user,
            'title' => 'Личный кабинет'
        ]);
    }

    /**
     * Display the home page
     */
    public function index()
    {
        // Get featured properties (limit 6)
        // $featuredProperties = Property::featured()->limit(6)->get();
        
        // Get statistics
        $stats = [
            'total_properties' => 10000, // Property::count()
            'monthly_deals' => 500,
            'realtors_count' => 50,
            'years_experience' => 15
        ];
        
        return $this->view('home', [
            'stats' => $stats,
            // 'featuredProperties' => $featuredProperties
        ]);
    }

    /**
     * Display about page
     */
    public function about()
    {
        return $this->view('pages/about', [
            'title' => 'О компании'
        ]);
    }

    /**
     * Display services page
     */
    public function services()
    {
        $services = [
            [
                'title' => 'Покупка недвижимости',
                'description' => 'Помогаем найти и приобрести идеальную недвижимость',
                'features' => [
                    'Подбор объектов по критериям',
                    'Юридическое сопровождение',
                    'Помощь с ипотекой',
                    'Проверка документов'
                ]
            ],
            [
                'title' => 'Продажа недвижимости',
                'description' => 'Эффективная продажа вашей недвижимости',
                'features' => [
                    'Оценка рыночной стоимости',
                    'Профессиональная фотосъёмка',
                    'Размещение на всех площадках',
                    'Сопровождение сделки'
                ]
            ],
            [
                'title' => 'Аренда жилья',
                'description' => 'Поиск арендного жилья и управление арендой',
                'features' => [
                    'База проверенных объектов',
                    'Юридическая поддержка',
                    'Управление арендой',
                    'Решение споров'
                ]
            ]
        ];

        return $this->view('pages/services', [
            'title' => 'Наши услуги',
            'services' => $services
        ]);
    }

    /**
     * Display contact page and handle contact form
     */
    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleContactForm();
        }

        return $this->view('pages/contact', [
            'title' => 'Контакты'
        ]);
    }

    /**
     * Handle contact form submission
     */
    protected function handleContactForm()
    {
        $this->validate([
            'name' => 'required|min:2|max:100',
            'phone' => 'required|min:10|max:20',
            'email' => 'email|max:255',
            'service' => 'required|in:buy,sell,rent,lease,evaluate',
            'message' => 'max:1000'
        ]);

        $data = [
            'name' => trim($_POST['name']),
            'phone' => trim($_POST['phone']),
            'email' => trim($_POST['email'] ?? ''),
            'service' => $_POST['service'],
            'message' => trim($_POST['message'] ?? ''),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Save to database
            // ContactRequest::create($data);

            // Send notification email
            // $this->sendContactNotification($data);

            // Log the request
            logger()->info('Contact form submitted', $data);

            if ($this->request->isAjax()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Спасибо! Мы свяжемся с вами в ближайшее время.'
                ]);
            }

            session()->flash('success', 'Спасибо! Мы свяжемся с вами в ближайшее время.');
            return redirect('/contact');

        } catch (Exception $e) {
            logger()->error('Contact form error: ' . $e->getMessage());

            if ($this->request->isAjax()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Произошла ошибка. Попробуйте позже или позвоните нам.'
                ], 500);
            }

            session()->flash('error', 'Произошла ошибка. Попробуйте позже или позвоните нам.');
            return redirect('/contact');
        }
    }

    /**
     * Send contact notification email
     */
    protected function sendContactNotification($data)
    {
        $subject = 'Новая заявка с сайта - ' . $data['service'];
        $message = "
            Новая заявка с сайта:\n\n
            Имя: {$data['name']}\n
            Телефон: {$data['phone']}\n
            Email: {$data['email']}\n
            Услуга: {$data['service']}\n
            Сообщение: {$data['message']}\n\n
            IP: {$data['ip_address']}\n
            User Agent: {$data['user_agent']}\n
            Время: {$data['created_at']}
        ";

        // Simple mail sending (in production use proper mail service)
        $adminEmail = config('app.admin_email', 'admin@example.com');
        $headers = "From: no-reply@{$_SERVER['HTTP_HOST']}\r\n";
        $headers .= "Reply-To: {$data['email']}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($adminEmail, $subject, $message, $headers);
    }

    /**
     * Privacy policy page
     */
    public function privacy()
    {
        return $this->view('pages/privacy', [
            'title' => 'Политика конфиденциальности'
        ]);
    }

    /**
     * Terms of service page
     */
    public function terms()
    {
        return $this->view('pages/terms', [
            'title' => 'Пользовательское соглашение'
        ]);
    }

    /**
     * Search API endpoint
     */
    public function search()
    {
        $query = trim($_GET['q'] ?? '');
        
        if (strlen($query) < 2) {
            return $this->json([]);
        }

        // Search in properties
        // $results = Property::search($query)->limit(10)->get();
        
        // Mock results for now
        $results = [];
        for ($i = 1; $i <= 5; $i++) {
            if (stripos("Квартира в центре {$i}", $query) !== false) {
                $results[] = [
                    'id' => $i,
                    'title' => "Квартира в центре {$i}",
                    'address' => "ул. Тверская, {$i}",
                    'price' => 5000000 + $i * 500000,
                    'type' => 'apartment'
                ];
            }
        }

        return $this->json($results);
    }

    /**
     * Site map (for SEO)
     */
    public function sitemap()
    {
        header('Content-Type: application/xml; charset=utf-8');
        
        $urls = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/properties', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/services', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'weekly']
        ];

        // Add property URLs
        // $properties = Property::published()->get();
        // foreach ($properties as $property) {
        //     $urls[] = [
        //         'loc' => "/properties/{$property->id}",
        //         'priority' => '0.8',
        //         'changefreq' => 'weekly',
        //         'lastmod' => $property->updated_at
        //     ];
        // }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars(url($url['loc'])) . "</loc>\n";
            echo "    <priority>{$url['priority']}</priority>\n";
            echo "    <changefreq>{$url['changefreq']}</changefreq>\n";
            if (isset($url['lastmod'])) {
                echo "    <lastmod>{$url['lastmod']}</lastmod>\n";
            }
            echo "  </url>\n";
        }
        
        echo '</urlset>';
        exit;
    }
}