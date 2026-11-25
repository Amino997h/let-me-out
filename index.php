<?php
require_once __DIR__ . '/config.php';

$pdo = getDB();
$category = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';

$opportunities = [];

if ($pdo) {
    try {
        $sql = "SELECT * FROM opportunities WHERE status = 'active'";
        $params = [];

        if ($category !== 'all' && array_key_exists($category, $CATEGORIES)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY is_featured DESC, id DESC LIMIT 50";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $opportunities = $stmt->fetchAll();
    } catch (Exception $e) {
        $opportunities = [];
    }
}

// عينات افتراضية أنيقة في حال عدم الربط بقاعدة البيانات
if (empty($opportunities) && !$pdo) {
    $opportunities = [
        [
            'id' => 1,
            'title' => 'برنامج قرعة الهجرة التعددية إلى الولايات المتحدة (DV Lottery)',
            'country' => 'الولايات المتحدة',
            'category' => 'immigration',
            'funding_type' => 'fully_funded',
            'summary' => 'التقديم السنوي الرسمي للحصول على الإقامة الدائمة وتصريح العمل في أمريكا.',
            'deadline' => '2026-11-05',
            'is_featured' => 1,
            'apply_link' => 'https://dvprogram.state.gov/'
        ],
        [
            'id' => 2,
            'title' => 'منحة الحكومة التركية للعام الأكاديمي 2026',
            'country' => 'تركيا',
            'category' => 'scholarship',
            'funding_type' => 'fully_funded',
            'summary' => 'تغطية شاملة للرسوم الدراسية مع توفير السكن وتذاكر السفر وراتب شهري وتأمين صحي.',
            'deadline' => '2026-02-20',
            'is_featured' => 1,
            'apply_link' => 'https://tbbs.turkiyeburslari.gov.tr/'
        ],
        [
            'id' => 3,
            'title' => 'برنامج التدريب المهني المزدوج (Ausbildung)',
            'country' => 'ألمانيا',
            'category' => 'job',
            'funding_type' => 'paid_job',
            'summary' => 'دراسة وتدريب عملي مدفوع براتب شهري مع فرصة الاستقرار والعمل بعد التخرج.',
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
  <title><?= SITE_NAME ?> — <?= SITE_SLOGAN ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700&family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    body {
      font-family: 'Tajawal', sans-serif;
      -webkit-tap-highlight-color: transparent;
    }
    .font-brand {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-white text-zinc-900 min-h-screen flex flex-col antialiased">

  <!-- شريط الملاحة العلوي فائق البساطة -->
  <header class="border-b border-zinc-200/80 bg-white/95 backdrop-blur-sm sticky top-0 z-40">
    <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-2">
        <span class="font-brand font-bold text-lg tracking-tight text-zinc-950">Let Me Out</span>
      </a>

      <div class="flex items-center gap-3">
        <a href="admin/index.php" class="text-xs font-medium text-zinc-500 hover:text-zinc-950 px-2.5 py-1 rounded-lg hover:bg-zinc-100 transition">
          لوحة الإدارة
        </a>
      </div>
    </div>
  </header>

  <!-- ترويسة الصفحة هادئة وأنيقة -->
  <section class="border-b border-zinc-100 pt-8 pb-6 px-4">
    <div class="max-w-4xl mx-auto text-right">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-950 tracking-tight leading-snug mb-2">
        دليلك المباشر لفرص الهجرة، العمل، والمنح
      </h1>
      <p class="text-xs sm:text-sm text-zinc-500 max-w-xl leading-relaxed">
        معلومات موثوقة ومحدثة مع روابط التقديم الرسمية مباشرة للمصادر.
      </p>
    </div>
  </section>

  <!-- فلاتر تصنيفية نظيفة وبسيطة (Tabs) -->
  <div class="border-b border-zinc-200/80 bg-white sticky top-14 z-30">
    <div class="max-w-4xl mx-auto px-4 py-2 flex items-center gap-2 overflow-x-auto no-scrollbar">
      <?php foreach ($CATEGORIES as $key => $name): ?>
        <?php $isActive = ($category === $key); ?>
        <a href="index.php?cat=<?= $key ?>" 
           class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-medium transition <?= $isActive ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100' ?>">
          <?= $name ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- قائمة الفرص بتصميم نقي ودقيق للهاتف -->
  <main class="max-w-4xl mx-auto px-4 py-6 flex-1 w-full">
    
    <div class="flex items-center justify-between mb-4">
      <span class="text-xs font-semibold text-zinc-400">
        <?= count($opportunities) ?> فرصة معروضة
      </span>
      <?php if ($category !== 'all'): ?>
        <a href="index.php" class="text-xs font-medium text-zinc-500 hover:text-zinc-950">إعادة تعيين الفلتر</a>
      <?php endif; ?>
    </div>

    <?php if (empty($opportunities)): ?>
      <div class="text-center py-16 px-4 border border-zinc-200 rounded-2xl bg-zinc-50/50 my-4">
        <p class="text-sm font-semibold text-zinc-800 mb-1">لا توجد فرص متاحة في هذا القسم حالياً</p>
        <p class="text-xs text-zinc-500 mb-4">تابعنا، يتم تحديث وإضافة الفرص أولاً بأول.</p>
        <a href="index.php" class="inline-block px-4 py-2 bg-zinc-950 text-white text-xs font-medium rounded-lg">عرض جميع الفرص</a>
      </div>
    <?php else: ?>
      <div class="divide-y divide-zinc-100 border-t border-b border-zinc-100">
        <?php foreach ($opportunities as $opp): ?>
          <?php 
            $fundingInfo = $FUNDING_TYPES[$opp['funding_type']] ?? $FUNDING_TYPES['fully_funded'];
          ?>
          <article class="py-5 px-1 hover:bg-zinc-50/70 transition-colors rounded-xl">
            <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1.5 mb-2">
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-zinc-900 bg-zinc-100 px-2 py-0.5 rounded">
                  <?= htmlspecialchars($opp['country']) ?>
                </span>
                <span class="text-[11px] font-medium px-2 py-0.5 rounded <?= $fundingInfo['class'] ?>">
                  <?= $fundingInfo['label'] ?>
                </span>
                <span class="text-[11px] text-zinc-400">
                  <?= $CATEGORIES[$opp['category']] ?? '' ?>
                </span>
              </div>

              <?php if (!empty($opp['deadline'])): ?>
                <span class="text-[11px] text-zinc-400 font-mono">
                  الموعد: <?= htmlspecialchars($opp['deadline']) ?>
                </span>
              <?php endif; ?>
            </div>

            <h2 class="text-base sm:text-lg font-bold text-zinc-950 hover:text-zinc-600 transition mb-2">
              <a href="details.php?id=<?= $opp['id'] ?>" class="block">
                <?= htmlspecialchars($opp['title']) ?>
              </a>
            </h2>

            <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed line-clamp-2 mb-3">
              <?= htmlspecialchars($opp['summary']) ?>
            </p>

            <div>
              <a href="details.php?id=<?= $opp['id'] ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-950 hover:underline">
                <span>التفاصيل والشروط</span>
                <span class="text-[10px]">←</span>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <!-- تذييل بسيط وخفيف -->
  <footer class="border-t border-zinc-200 py-6 text-center text-xs text-zinc-400 mt-auto">
    <p>© 2026 Let Me Out. جميع المعلومات مأخوذة من مصادرها الرسمية.</p>
  </footer>

</body>
</html>
