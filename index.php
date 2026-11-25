<?php
require_once __DIR__ . '/config.php';

$pdo = getDB();
$category = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$opportunities = [];

if ($pdo) {
    try {
        $sql = "SELECT * FROM opportunities WHERE status = 'active'";
        $params = [];

        if ($category !== 'all' && array_key_exists($category, $CATEGORIES)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR country LIKE ? OR summary LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY is_featured DESC, id DESC LIMIT 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $opportunities = $stmt->fetchAll();
    } catch (Exception $e) {
        $opportunities = [];
    }
}

// عينات احتياطية في حال لم تكن قاعدة البيانات مربوطة بعد على السيرفر
if (empty($opportunities) && !$pdo) {
    $opportunities = [
        [
            'id' => 1,
            'title' => 'برنامج الهجرة العشوائية لأمريكا (اللوري DV)',
            'country' => 'الولايات المتحدة',
            'country_code' => '🇺🇸',
            'category' => 'immigration',
            'funding_type' => 'fully_funded',
            'summary' => 'الفرصة السنوية الأكبر للحصول على الجرين كارد والإقامة الدائمة الأمريكية مجاناً وبدون تعقيدات لغوية.',
            'deadline' => '2026-11-05',
            'is_featured' => 1,
            'apply_link' => 'https://dvprogram.state.gov/'
        ],
        [
            'id' => 2,
            'title' => 'منحة الحكومة التركية Türkiye Bursları 2026',
            'country' => 'تركيا',
            'country_code' => '🇹🇷',
            'category' => 'scholarship',
            'funding_type' => 'fully_funded',
            'summary' => 'تشمل الرسوم الدراسية بنسبة 100%، سكن جامعي مجاني، تذاكر طيران، وراتب شهري وتأمين صحي.',
            'deadline' => '2026-02-20',
            'is_featured' => 1,
            'apply_link' => 'https://tbbs.turkiyeburslari.gov.tr/'
        ],
        [
            'id' => 3,
            'title' => 'عقود تدريب مهني وفرص عمل أوسبيلدونغ (Ausbildung)',
            'country' => 'ألمانيا',
            'country_code' => '🇩🇪',
            'category' => 'job',
            'funding_type' => 'paid_job',
            'summary' => 'دراسة وتدريب عملي براتب شهري يبدأ من 1000 يورو مع فرصة البقاء والعمل الدائم في ألمانيا.',
            'deadline' => '2026-06-15',
            'is_featured' => 0,
            'apply_link' => '#'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= SITE_NAME ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      -webkit-tap-highlight-color: transparent;
    }
    /* إخفاء شريط التمرير الأفقي للأقسام السريعة على الهاتف */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-indigo-600 selection:text-white">

  <!-- شريط علوي أنيق للهاتف وسطح المكتب -->
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
    <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-2.5">
        <span class="w-10 h-10 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-md shadow-indigo-100">✈️</span>
        <div>
          <span class="text-xl font-black text-slate-900 tracking-tight">Let Me Out</span>
          <span class="hidden sm:inline-block text-xs text-indigo-600 font-bold mr-1">| فرص الخارج</span>
        </div>
      </a>

      <div class="flex items-center gap-2">
        <a href="admin/index.php" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          <span>لوحة التحكم</span>
        </a>
      </div>
    </div>
  </header>

  <!-- بطل الصفحة (Hero Header) للهاتف -->
  <div class="bg-gradient-to-b from-indigo-50/70 to-slate-50 border-b border-slate-200/60 pt-6 pb-4 px-4">
    <div class="max-w-3xl mx-auto text-center">
      <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight mb-2">
        كل فرص الخروج للهجرة، العمل، والمنح في مكان واحد 🌍
      </h1>
      <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto mb-4">
        معلومات طازجة وموثوقة، مع روابط التقديم الرسمية المباشرة بدون وسطاء.
      </p>

      <!-- حقل البحث السريع المناسب لشاشات اللمس -->
      <form method="GET" action="index.php" class="relative max-w-md mx-auto">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن دولة، منحة، أو فرصة عمل..." class="w-full pl-10 pr-4 py-3 text-sm bg-white border border-slate-300 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition">
        <button type="submit" class="absolute left-2.5 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-indigo-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>
      </form>
    </div>
  </div>

  <!-- تصنيفات أفقية سريعة التمرير (Horizontal Scrollable Chips for Mobile) -->
  <div class="bg-white border-b border-slate-200/80 sticky top-16 z-30">
    <div class="max-w-5xl mx-auto px-4 py-2.5 flex items-center gap-2 overflow-x-auto no-scrollbar">
      <?php foreach ($CATEGORIES as $key => $name): ?>
        <?php $isActive = ($category === $key); ?>
        <a href="index.php?cat=<?= $key ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" 
           class="whitespace-nowrap px-3.5 py-1.5 rounded-full text-xs font-semibold transition <?= $isActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
          <?= $name ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- قائمة الفرص (Cards Grid) -->
  <main class="max-w-5xl mx-auto px-4 py-6 flex-1 w-full">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
        <span>⚡ أحدث الفرص المتاحة</span>
        <span class="text-xs px-2 py-0.5 rounded-md bg-slate-200 text-slate-700"><?= count($opportunities) ?></span>
      </h2>
      <?php if (!empty($search) || $category !== 'all'): ?>
        <a href="index.php" class="text-xs font-semibold text-indigo-600 hover:underline">عرض الكل</a>
      <?php endif; ?>
    </div>

    <?php if (empty($opportunities)): ?>
      <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300 p-8 my-6">
        <span class="text-4xl mb-3 block">🔍</span>
        <h3 class="text-base font-bold text-slate-800 mb-1">لم يتم العثور على فرص مطابقة</h3>
        <p class="text-xs text-slate-500 mb-4">جرب البحث بكلمات أخرى أو تغيير التصنيف.</p>
        <a href="index.php" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow">إعادة تعيين البحث</a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($opportunities as $opp): ?>
          <?php 
            $fundingInfo = $FUNDING_TYPES[$opp['funding_type']] ?? $FUNDING_TYPES['fully_funded'];
          ?>
          <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
            
            <?php if (!empty($opp['is_featured'])): ?>
              <div class="absolute top-0 left-0 bg-amber-500 text-white text-[10px] font-bold px-3 py-0.5 rounded-br-xl shadow-xs">
                ⭐ فرصة مميزة
              </div>
            <?php endif; ?>

            <div>
              <!-- الدولة ونوع التمويل -->
              <div class="flex items-center justify-between gap-2 mb-2.5">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                  <span><?= htmlspecialchars($opp['country_code'] ?? '🌍') ?></span>
                  <span><?= htmlspecialchars($opp['country']) ?></span>
                </span>
                
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border <?= $fundingInfo['color'] ?>">
                  <?= $fundingInfo['label'] ?>
                </span>
              </div>

              <!-- العنوان -->
              <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition mb-2 leading-snug">
                <a href="details.php?id=<?= $opp['id'] ?>" class="stretched-link">
                  <?= htmlspecialchars($opp['title']) ?>
                </a>
              </h3>

              <!-- نبذة سريعة -->
              <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-4">
                <?= htmlspecialchars($opp['summary']) ?>
              </p>
            </div>

            <!-- الجزء السفلي: الموعد وزر التقديم -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
              <div class="flex items-center gap-1 text-slate-500 text-[11px]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>آخر موعد: <?= !empty($opp['deadline']) ? htmlspecialchars($opp['deadline']) : 'مفتوح دائماً' ?></span>
              </div>

              <a href="details.php?id=<?= $opp['id'] ?>" class="inline-flex items-center gap-1 font-bold text-indigo-600 hover:text-indigo-700 py-1">
                <span>التفاصيل والشروط</span>
                <span>←</span>
              </a>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- شريط تنقل سفلي ثابت خاص بالهواتف لسهولة الاستخدام (Mobile Bottom Bar) -->
  <nav class="sm:hidden sticky bottom-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-6 py-2 flex justify-around items-center">
    <a href="index.php" class="flex flex-col items-center text-indigo-600 font-bold text-[10px]">
      <span class="text-lg">🏠</span>
      <span>الرئيسية</span>
    </a>
    <a href="index.php?cat=scholarship" class="flex flex-col items-center text-slate-500 hover:text-indigo-600 font-semibold text-[10px]">
      <span class="text-lg">🎓</span>
      <span>منح</span>
    </a>
    <a href="index.php?cat=job" class="flex flex-col items-center text-slate-500 hover:text-indigo-600 font-semibold text-[10px]">
      <span class="text-lg">💼</span>
      <span>وظائف</span>
    </a>
    <a href="index.php?cat=immigration" class="flex flex-col items-center text-slate-500 hover:text-indigo-600 font-semibold text-[10px]">
      <span class="text-lg">🛂</span>
      <span>هجرة</span>
    </a>
  </nav>

  <!-- التذييل لسطح المكتب -->
  <footer class="hidden sm:block bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
    <p>© 2026 Let Me Out - منصتك لفرص الهجرة والعمل والدراسة بالخارج.</p>
  </footer>

</body>
</html>
