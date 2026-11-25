<?php
// إعدادات الاتصال بقاعدة بيانات InfinityFree أو السيرفر المحلي
define('DB_HOST', getenv('DB_HOST') ?: 'sql301.infinityfree.com');
define('DB_USER', getenv('DB_USER') ?: 'if0_42610278');
define('DB_PASS', getenv('DB_PASS') ?: '8tFsRQMj1inUgKo');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_42610278_LTO');

// اسم الموقع والروابط
define('SITE_NAME', 'Let Me Out');
define('SITE_SLOGAN', 'دليلك المباشر لفرص الهجرة والعمل والدراسة بالخارج');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            return null;
        }
    }
    return $pdo;
}

// تصنيفات أنيقة وبدون إيموجي
$CATEGORIES = [
    'all' => 'جميع الفرص',
    'immigration' => 'الهجرة والإقامة',
    'scholarship' => 'المنح الدراسية',
    'job' => 'عقود العمل',
    'volunteering' => 'برامج التطوع',
    'nomad' => 'العمل عن بعد',
    'internship' => 'التدريب المهني',
    'other' => 'أخرى'
];

// أنواع التمويل
$FUNDING_TYPES = [
    'fully_funded' => ['label' => 'تمويل كامل', 'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/60'],
    'partially_funded' => ['label' => 'تمويل جزئي', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200/60'],
    'paid_job' => ['label' => 'وظيفة براتب', 'class' => 'bg-zinc-100 text-zinc-800 border border-zinc-200'],
    'self_funded' => ['label' => 'حساب خاص', 'class' => 'bg-zinc-50 text-zinc-600 border border-zinc-200/60'],
];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
