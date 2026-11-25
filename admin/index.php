<?php
require_once __DIR__ . '/../config.php';

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
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = 'admin';
            header("Location: index.php");
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة';
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
                VALUES (?, ?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $title, $slug, $country, $category, $funding_type,
            $summary, $description, $requirements, $benefits, $apply_link, $deadline, $is_featured
        ]);
        $success = 'تم نشر الفرصة بنجاح على الموقع.';
    } else {
        $error = 'يرجى ملء العنوان ورابط التقديم.';
    }
}

// جلب كل الفرص
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
  <title>لوحة الإدارة — <?= SITE_NAME ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600&family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style> body { font-family: 'Tajawal', sans-serif; } .font-brand { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-zinc-50 text-zinc-900 min-h-screen">

  <?php if (!$isLoggedIn): ?>
    <div class="min-h-screen flex items-center justify-center p-4">
      <div class="bg-white max-w-sm w-full p-6 sm:p-8 rounded-2xl border border-zinc-200">
        <div class="mb-6">
          <span class="font-brand font-bold text-lg text-zinc-950 block mb-1">Let Me Out</span>
          <h1 class="text-base font-bold text-zinc-800">تسجيل الدخول للإدارة</h1>
        </div>

        <?php if (!empty($error)): ?>
          <div class="bg-red-50 text-red-700 text-xs p-3 rounded-lg mb-4 border border-red-200">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="login">
          <div>
            <label class="block text-xs font-semibold text-zinc-700 mb-1">اسم المستخدم</label>
            <input type="text" name="username" required placeholder="admin" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
          </div>
          <div>
            <label class="block text-xs font-semibold text-zinc-700 mb-1">كلمة المرور</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
          </div>
          <button type="submit" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs sm:text-sm transition">
            دخول
          </button>
        </form>
        <div class="mt-4 text-center">
          <a href="../index.php" class="text-xs text-zinc-500 hover:text-zinc-950">← العودة للموقع</a>
        </div>
      </div>
    </div>
  <?php else: ?>

    <header class="bg-white border-b border-zinc-200 sticky top-0 z-30">
      <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
        <span class="font-brand font-bold text-sm text-zinc-950">Let Me Out — الإدارة</span>
        <div class="flex items-center gap-3">
          <a href="../index.php" target="_blank" class="text-xs text-zinc-600 hover:text-zinc-950">معاينة الموقع ↗</a>
          <a href="index.php?logout=1" class="text-xs text-red-600 hover:underline">تسجيل الخروج</a>
        </div>
      </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-6">

      <?php if (!empty($success)): ?>
        <div class="bg-emerald-50 text-emerald-800 text-xs p-3 rounded-lg mb-5 border border-emerald-200 font-medium">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <!-- نموذج إضافة فرصة -->
      <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6 mb-8">
        <h2 class="text-sm font-bold text-zinc-950 mb-4 pb-2 border-b border-zinc-100">
          إضافة فرصة جديدة
        </h2>

        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="add_opportunity">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-zinc-700 mb-1">عنوان الفرصة *</label>
              <input type="text" name="title" required placeholder="مثال: منحة الحكومة الكندية للماجستير والدكتوراه 2026" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">الدولة *</label>
              <input type="text" name="country" required placeholder="كندا" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">نوع الفرصة *</label>
              <select name="category" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900 bg-white">
                <option value="immigration">الهجرة والإقامة</option>
                <option value="scholarship">منحة دراسية</option>
                <option value="job">عقد عمل</option>
                <option value="volunteering">برنامج تطوع</option>
                <option value="nomad">العمل عن بعد</option>
                <option value="internship">تدريب مهني</option>
                <option value="other">أخرى</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">نوع التمويل *</label>
              <select name="funding_type" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900 bg-white">
                <option value="fully_funded">تمويل كامل</option>
                <option value="paid_job">وظيفة براتب شهري</option>
                <option value="partially_funded">تمويل جزئي</option>
                <option value="self_funded">حساب خاص</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">آخر موعد للتقديم</label>
              <input type="date" name="deadline" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900 bg-white">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-zinc-700 mb-1">ملخص مختصر يظهر في القائمة *</label>
              <input type="text" name="summary" required placeholder="جملة أو جملتان توضحان أهم ما في الفرصة" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-zinc-700 mb-1">تفاصيل الفرصة</label>
              <textarea name="description" rows="3" placeholder="الشرح الكامل للفرصة..." class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900"></textarea>
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">المميزات والتمويل</label>
              <textarea name="benefits" rows="3" placeholder="- سكن مجاني&#10;- راتب شهري" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900"></textarea>
            </div>

            <div>
              <label class="block text-xs font-semibold text-zinc-700 mb-1">الشروط والأوراق</label>
              <textarea name="requirements" rows="3" placeholder="- جواز سفر ساري&#10;- سيرة ذاتية" class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900"></textarea>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-zinc-700 mb-1">رابط التقديم الرسمي المباشر *</label>
              <input type="url" name="apply_link" required placeholder="https://..." class="w-full px-3 py-2 rounded-lg border border-zinc-300 text-xs sm:text-sm focus:outline-none focus:border-zinc-900">
            </div>
          </div>

          <div class="flex items-center gap-2 pt-1">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" class="rounded border-zinc-300 text-zinc-900">
            <label for="is_featured" class="text-xs text-zinc-700">تثبيت كفرصة مميزة في الأعلى</label>
          </div>

          <button type="submit" class="px-6 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs sm:text-sm transition">
            نشر الفرصة
          </button>
        </form>
      </div>

      <!-- قائمة الفرص الحالية -->
      <div class="bg-white rounded-2xl border border-zinc-200 p-5 sm:p-6">
        <h3 class="text-xs font-bold text-zinc-700 mb-4">الفرص المضافة (<?= count($list) ?>)</h3>
        
        <?php if (empty($list)): ?>
          <p class="text-xs text-zinc-400 text-center py-6">لا توجد فرص مضافة حتى الآن.</p>
        <?php else: ?>
          <div class="divide-y divide-zinc-100">
            <?php foreach ($list as $item): ?>
              <div class="py-3 flex items-center justify-between gap-3">
                <div>
                  <h4 class="text-xs sm:text-sm font-bold text-zinc-900"><?= htmlspecialchars($item['title']) ?></h4>
                  <span class="text-[11px] text-zinc-400"><?= htmlspecialchars($item['country']) ?> • الموعد: <?= $item['deadline'] ?: 'مفتوح' ?></span>
                </div>
                <div class="flex items-center gap-3">
                  <a href="../details.php?id=<?= $item['id'] ?>" target="_blank" class="text-xs text-zinc-600 hover:text-zinc-950">معاينة</a>
                  <a href="index.php?delete=<?= $item['id'] ?>" onclick="return confirm('تأكيد الحذف؟')" class="text-xs text-red-600 hover:underline">حذف</a>
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
