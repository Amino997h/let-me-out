<?php
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDB();

$opp = null;
if ($pdo && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM opportunities WHERE id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$id]);
        $opp = $stmt->fetch();

        if ($opp) {
            $pdo->prepare("UPDATE opportunities SET views = views + 1 WHERE id = ?")->execute([$id]);
        }
    } catch (Exception $e) {}
}

// في حال كانت تجربة محلية بدون DB
if (!$opp) {
    $opp = [
        'id' => $id,
        'title' => 'فرصة السفر والهجرة المحددة',
        'country' => 'دولي',
        'country_code' => '🌍',
        'category' => 'immigration',
        'funding_type' => 'fully_funded',
        'summary' => 'هذه تفاصيل الفرصة المتاحة للمتقدمين.',
        'description' => "تفاصيل هذه الفرصة تشمل توفير الإقامة، تصريح العمل، والتأمين الصحي الكامل لكافة المقبولين.\n\nتأكد من قراءة كافة الشروط وتجهيز الأوراق المطلوبة قبل التقديم عبر الرابط الرسمي المرفق أدناه.",
        'requirements' => "1. جواز سفر ساري المفعول لمدة 6 أشهر على الأقل.\n2. سيرة ذاتية حديثة بالنموذج الأوروبي أو الكندي.\n3. شهادة المؤهل العلمي وبيان الدرجات.",
        'benefits' => "1. راتب شهري أو تمويل كامل للنفقات.\n2. تأمين صحي شامل.\n3. تغطية تذاكر السفر وتسهيل إجراءات التأشيرة.",
        'deadline' => '2026-12-31',
        'apply_link' => 'https://google.com'
    ];
}

$fundingInfo = $FUNDING_TYPES[$opp['funding_type']] ?? $FUNDING_TYPES['fully_funded'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= htmlspecialchars($opp['title']) ?> | <?= SITE_NAME ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    body { font-family: 'Tajawal', sans-serif; -webkit-tap-highlight-color: transparent; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col pb-24 sm:pb-8 antialiased">

  <!-- شريط علوي -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between">
      <a href="index.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-indigo-600">
        <span>→</span>
        <span>العودة للفرص</span>
      </a>
      <span class="text-xs font-bold text-slate-400">تفاصيل الفرصة</span>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-6 w-full flex-1">
    
    <!-- بطاقة رأسية -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-7 shadow-xs mb-6">
      
      <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
          <span><?= htmlspecialchars($opp['country_code'] ?? '🌍') ?></span>
          <span><?= htmlspecialchars($opp['country']) ?></span>
        </span>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full border <?= $fundingInfo['color'] ?>">
          <?= $fundingInfo['label'] ?>
        </span>
        <span class="text-xs text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
          <?= $CATEGORIES[$opp['category']] ?? 'فرصة' ?>
        </span>
      </div>

      <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug mb-3">
        <?= htmlspecialchars($opp['title']) ?>
      </h1>

      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
        <?= htmlspecialchars($opp['summary']) ?>
      </p>

      <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200/60 px-3 py-2 rounded-xl">
        <span>⏰</span>
        <span><strong>آخر موعد للتقديم:</strong> <?= !empty($opp['deadline']) ? htmlspecialchars($opp['deadline']) : 'مفتوح باستمرار' ?></span>
      </div>
    </div>

    <!-- التفاصيل والشرح -->
    <div class="space-y-4">
      
      <?php if (!empty($opp['description'])): ?>
      <div class="bg-white rounded-2xl border border-slate-200/80 p-5">
        <h2 class="text-sm font-bold text-slate-900 mb-2 flex items-center gap-2">
          <span>📝</span>
          <span>عن الفرصة وتفاصيلها</span>
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['description']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($opp['benefits'])): ?>
      <div class="bg-white rounded-2xl border border-slate-200/80 p-5">
        <h2 class="text-sm font-bold text-emerald-800 mb-2 flex items-center gap-2">
          <span>🎁</span>
          <span>المميزات والتمويل المقدم</span>
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['benefits']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($opp['requirements'])): ?>
      <div class="bg-white rounded-2xl border border-slate-200/80 p-5">
        <h2 class="text-sm font-bold text-indigo-900 mb-2 flex items-center gap-2">
          <span>📋</span>
          <span>الشروط والمستندات المطلوبة</span>
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['requirements']) ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

  </main>

  <!-- زر التقديم الثابت في أسفل الهاتف (Sticky Bottom CTA for Mobile) -->
  <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 sm:py-4 z-50">
    <div class="max-w-3xl mx-auto flex items-center gap-3">
      <a href="index.php" class="p-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      </a>
      <a href="<?= htmlspecialchars($opp['apply_link']) ?>" target="_blank" rel="noopener noreferrer" class="flex-1 py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-sm text-center rounded-2xl shadow-lg shadow-indigo-100 flex items-center justify-center gap-2 transition">
        <span>التقديم على الموقع الرسمي المباشر</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
      </a>
    </div>
  </div>

</body>
</html>
