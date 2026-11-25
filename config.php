<?php
// إعدادات الاتصال بقاعدة بيانات InfinityFree أو السيرفر المحلي
define('DB_HOST', getenv('DB_HOST') ?: 'sql301.infinityfree.com');
define('DB_USER', getenv('DB_USER') ?: 'if0_42610278');
define('DB_PASS', getenv('DB_PASS') ?: '8tFsRQMj1inUgKo');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_42610278_LTO');

// اسم الموقع والروابط
define('SITE_NAME', 'Let Me Out | فرص الهجرة والمنح');
define('SITE_SLOGAN', 'بوابتك الموثوقة لفرص الخروج، العمل، الدراسة، والتطوع بالخارج');

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
            // في حال عدم توفر اتصال بالإنترنت أو عدم تفعيل قاعدة البيانات بعد
            return null;
        }
    }
    return $pdo;
}

// مصفوفة التصنيفات
$CATEGORIES = [
    'all' => 'الكل',
    'immigration' => 'هجرة ومقاطعات 🛂',
    'scholarship' => 'منح دراسية 🎓',
    'job' => 'عقود عمل 💼',
    'volunteering' => 'تطوع وتبادل ثقافي 🤝',
    'nomad' => 'تأشيرات العمل الحر 💻',
    'internship' => 'تدريب مهني 🛠️',
    'other' => 'فرص أخرى ✈️'
];

// أنواع التمويل
$FUNDING_TYPES = [
    'fully_funded' => ['label' => 'ممولة بالكامل', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
    'partially_funded' => ['label' => 'ممولة جزئياً', 'color' => 'bg-blue-100 text-blue-800 border-blue-200'],
    'paid_job' => ['label' => 'وظيفة براتب', 'color' => 'bg-amber-100 text-amber-800 border-amber-200'],
    'self_funded' => ['label' => 'على الحساب الخاص', 'color' => 'bg-slate-100 text-slate-800 border-slate-200'],
];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
