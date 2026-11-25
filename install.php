<?php
// سكربت التثبيت التلقائي لقاعدة البيانات
require_once __DIR__ . '/config.php';

echo "<!DOCTYPE html><html lang='ar' dir='rtl'><head><meta charset='UTF-8'><title>تثبيت قاعدة البيانات</title><style>body{font-family:sans-serif;padding:30px;line-height:1.6;background:#f8fafc;color:#1e293b;max-width:600px;margin:auto;} .box{background:#fff;padding:24px;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);}</style></head><body><div class='box'>";
echo "<h2>🚀 جاري تثبيت قاعدة البيانات...</h2>";

$pdo = getDB();

if (!$pdo) {
    echo "<p style='color:red;'>❌ فشل الاتصال بقاعدة البيانات. تأكد من أنك قمت بإنشاء قاعدة البيانات <b>if0_42610278_LTO</b> في لوحة تحكم InfinityFree أولاً.</p>";
    echo "</div></body></html>";
    exit;
}

$sqlFile = __DIR__ . '/schema.sql';
if (!file_exists($sqlFile)) {
    echo "<p style='color:red;'>❌ ملف schema.sql غير موجود.</p></div></body></html>";
    exit;
}

$sql = file_get_contents($sqlFile);

try {
    $pdo->exec($sql);
    echo "<p style='color:green;font-weight:bold;'>✅ تم إنشاء الجداول وإدخال البيانات التجريبية بنجاح تام!</p>";
    echo "<p>بيانات الدخول للوحة التحكم:</p>";
    echo "<ul><li><b>الرابط:</b> <a href='admin/index.php'>/admin/index.php</a></li><li><b>المستخدم:</b> admin</li><li><b>كلمة المرور:</b> admin123</li></ul>";
    echo "<p><a href='index.php' style='display:inline-block;padding:10px 20px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:10px;margin-top:10px;'>الذهاب إلى الموقع الرئيسي ✈️</a></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ حدث خطأ أثناء تنفيذ الأوامر: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div></body></html>";
