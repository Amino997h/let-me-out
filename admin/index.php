<?php
require_once __DIR__ . '/../config.php';

// التحقق من تسجيل الدخول
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$error = '';
$success = '';

// تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $pdo = getDB();
    if ($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $admin['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة';
        }
    } else {
        // تسجيل دخول احتياطي إن لم تكن قاعدة البيانات مفعلة بعد
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = 'admin';
            header("Location: index.php");
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة (الافتراضي: admin / admin123)';
        }
    }
}

// تسجيل الخروج
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// حذف فرصة
if ($isLoggedIn && isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo = getDB();
    if ($pdo && $delId > 0) {
        $pdo->prepare("DELETE FROM opportunities WHERE id = ?")->execute([$delId]);
        header("Location: index.php?msg=deleted");
        exit;
    }
}

// إضافة فرصة جديدة
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_opportunity') {
    $title = trim($_POST['title'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $country_code = trim($_POST['country_code'] ?? '🌍');
    $category = trim($_POST['category'] ?? 'other');
    $funding_type = trim($_POST['funding_type'] ?? 'fully_funded');
    $summary = trim($_POST['summary'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $benefits = trim($_POST['benefits'] ?? '');
    $apply_link = trim($_POST['apply_link'] ?? '');
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $pdo = getDB();
    if ($pdo && !empty($title) && !empty($apply_link)) {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title)) . '-' . time();
        $sql = "INSERT INTO opportunities 
                (title, slug, country, country_code, category, funding_type, summary, description, requirements, benefits, apply_link, deadline, is_featured, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $slug, $country, $country_code, $category, $funding_type,
            $summary, $description, $requirements, $benefits, $apply_link, $deadline, $is_featured
        ]);
        $success = 'تم نشر الفرصة بنجاح على الموقع!';
    } else {
        $error = 'يرجى التأكد من ملء العنوان ورابط التقديم وتوفر الاتصال بقاعدة البيانات.';
    }
}

// جلب كل الفرص في لوحة التحكم
$list = [];
$pdo = getDB();
if ($pdo) {
    try {
        $list = $pdo->query("SELECT * FROM opportunities ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة التحكم | Let Me Out</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style> body { font-family: 'Tajawal', sans-serif; } </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">

  <?php if (!$isLoggedIn): ?>
    <!-- شاشة تسجيل الدخول -->
    <div class="min-h-screen flex items-center justify-center p-4">
      <div class="bg-white max-w-sm w-full p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
        <div class="text-center mb-6">
          <span class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3">🔒</span>
          <h1 class="text-xl font-extrabold text-slate-900">لوحة تحكم الإدارة</h1>
          <p class="text-xs text-slate-500 mt-1">خاصة بصاحب الموقع لإضافة الفرص</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="bg-red-50 text-red-700 text-xs p-3 rounded-xl mb-4 border border-red-200 font-medium">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="login">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">اسم المستخدم</label>
            <input type="text" name="username" required placeholder="admin" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">كلمة المرور</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
          <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
            تسجيل الدخول
          </button>
        </form>
        <div class="mt-4 text-center">
          <a href="../index.php" class="text-xs text-slate-500 hover:text-indigo-600">← العودة للموقع</a>
        </div>
      </div>
    </div>
  <?php else: ?>

    <!-- اللوحة الرئيسية بعد تسجيل الدخول -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
      <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="text-indigo-600 font-extrabold text-lg">⚡ إدارة فرص Let Me Out</span>
        </div>
        <div class="flex items-center gap-3">
          <a href="../index.php" target="_blank" class="text-xs text-slate-600 hover:text-indigo-600 font-bold">معاينة الموقع ↗</a>
          <a href="index.php?logout=1" class="text-xs text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg font-bold">خروج</a>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 py-6">

      <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 text-emerald-800 text-xs p-3.5 rounded-2xl mb-5 border border-emerald-200 font-bold">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <!-- نموذج إضافة فرصة جديدة مصمم للهاتف والكمبيوتر -->
      <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-xs mb-8">
        <h2 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
          <span>➕</span>
          <span>إضافة فرصة سفر أو منحة جديدة</span>
        </h2>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="add_opportunity">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">عنوان الفرصة *</label>
              <input type="text" name="title" required placeholder="مثال: منحة الحكومة الكندية للماجستير والدكتوراه 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">الدولة *</label>
              <input type="text" name="country" required placeholder="كندا" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">رمز علم الدولة (Emoji)</label>
              <input type="text" name="country_code" value="🇨🇦" placeholder="🇨🇦 أو 🇩🇪" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">نوع الفرصة *</label>
              <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none bg-white">
                <option value="immigration">هجرة ومقاطعات 🛂</option>
                <option value="scholarship">منحة دراسية 🎓</option>
                <option value="job">عقد عمل وتوظيف 💼</option>
                <option value="volunteering">تطوع وتبادل 🤝</option>
                <option value="nomad">تأشيرة عمل عن بعد 💻</option>
                <option value="internship">تدريب مهني 🛠️</option>
                <option value="other">أخرى ✈️</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">نوع التمويل *</label>
              <select name="funding_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none bg-white">
                <option value="fully_funded">ممولة بالكامل (تذاكر + راتب + سكن)</option>
                <option value="paid_job">وظيفة مدفوعة براتب شهري</option>
                <option value="partially_funded">ممولة جزئياً</option>
                <option value="self_funded">على الحساب الشخصي</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">نبذة سريعة تظهر في بطاقة الهاتف *</label>
              <input type="text" name="summary" required placeholder="جملتين تلخصان الفرصة للمستخدم عند التصفح" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">شرح الفرصة بالتفصيل</label>
              <textarea name="description" rows="3" placeholder="اكتب كل التفاصيل الإضافية هنا..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">المميزات والتمويل (كل ميزة في سطر)</label>
              <textarea name="benefits" rows="3" placeholder="- سكن مجاني&#10;- راتب شهري&#10;- تأمين طبي" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">الشروط والأوراق (كل شرط في سطر)</label>
              <textarea name="requirements" rows="3" placeholder="- السن أقل من 35 سنة&#10;- جواز سفر ساري&#10;- سيرة ذاتية" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">رابط التقديم الرسمي المباشر *</label>
              <input type="url" name="apply_link" required placeholder="https://..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">آخر موعد للتقديم (Deadline)</label>
              <input type="date" name="deadline" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none bg-white">
            </div>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" class="w-4 h-4 text-indigo-600 rounded">
            <label for="is_featured" class="text-xs font-bold text-slate-700">تثبيت كفرصة مميزة في أعلى الموقع ⭐</label>
          </div>

          <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-md shadow-indigo-100">
            نشر الفرصة الآن
          </button>
        </form>
      </div>

      <!-- جدول / قائمة الفرص الحالية -->
      <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 mb-4">الفرص المنشورة حالياً (<?= count($list) ?>)</h3>
        
        <?php if (empty($list)): ?>
          <p class="text-xs text-slate-400 text-center py-6">لا توجد فرص مضافة حتى الآن في قاعدة البيانات.</p>
        <?php else: ?>
          <div class="space-y-3">
            <?php foreach ($list as $item): ?>
              <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-3">
                  <span class="text-xl"><?= htmlspecialchars($item['country_code'] ?? '🌍') ?></span>
                  <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900"><?= htmlspecialchars($item['title']) ?></h4>
                    <span class="text-[11px] text-slate-500"><?= htmlspecialchars($item['country']) ?> • آخر موعد: <?= $item['deadline'] ?: 'مفتوح' ?></span>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <a href="../details.php?id=<?= $item['id'] ?>" target="_blank" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg text-xs font-bold">معاينة</a>
                  <a href="index.php?delete=<?= $item['id'] ?>" onclick="return confirm('هل أنت متأكد من الحذف؟')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg text-xs font-bold">حذف</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

  <?php endif; ?>

</body>
</html>
