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

if (!$opp) {
    $opp = [
        'id' => $id,
        'title' => 'تفاصيل الفرصة المعروضة',
        'country' => 'دولي',
        'category' => 'immigration',
        'funding_type' => 'fully_funded',
        'summary' => 'تفاصيل كاملة وشاملة حول متطلبات التقديم والإجراءات الرسمية.',
        'description' => "تشمل هذه الفرصة التسهيلات اللازمة للإقامة وتصاريح السفر، مع التوجيه المباشر للمنصة الرسمية لاستكمال الإجراءات.\n\nيُرجى قراءة الشروط ومراجعة تاريخ الموعد النهائي بدقة قبل إرسال الطلب.",
        'requirements' => "1. وثيقة سفر سارية الصلاحية.\n2. سيرة ذاتية مفصلة ومحدثة.\n3. الشهادات والوثائق الأكاديمية أو المهنية المعتمدة.",
        'benefits' => "1. تغطية تكاليف البرنامج أو رسوم الدراسة.\n2. تأمين صحي ورعاية شاملة.\n3. بدلات دعم معيشي وتسهيل إجراءات التأشيرة.",
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
  <title><?= htmlspecialchars($opp['title']) ?> — <?= SITE_NAME ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700&family=Tajawal:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    body { font-family: 'Tajawal', sans-serif; -webkit-tap-highlight-color: transparent; }
    .font-brand { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-white text-zinc-900 min-h-screen flex flex-col pb-24 antialiased">

  <!-- شريط علوي بسيط -->
  <header class="border-b border-zinc-200/80 bg-white sticky top-0 z-40">
    <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between">
      <a href="index.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-950">
        <span>→</span>
        <span>العودة للرئيسية</span>
      </a>
      <span class="font-brand font-bold text-sm text-zinc-950">Let Me Out</span>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8 w-full flex-1">
    
    <div class="mb-6">
      <div class="flex flex-wrap items-center gap-2 mb-3">
        <span class="text-xs font-semibold text-zinc-900 bg-zinc-100 px-2.5 py-1 rounded">
          <?= htmlspecialchars($opp['country']) ?>
        </span>
        <span class="text-xs font-medium px-2.5 py-1 rounded <?= $fundingInfo['class'] ?>">
          <?= $fundingInfo['label'] ?>
        </span>
        <span class="text-xs text-zinc-500">
          <?= $CATEGORIES[$opp['category']] ?? '' ?>
        </span>
      </div>

      <h1 class="text-xl sm:text-3xl font-extrabold text-zinc-950 leading-snug mb-3">
        <?= htmlspecialchars($opp['title']) ?>
      </h1>

      <?php if (!empty($opp['deadline'])): ?>
        <p class="text-xs text-zinc-500 font-mono">
          آخر موعد للتقديم: <?= htmlspecialchars($opp['deadline']) ?>
        </p>
      <?php endif; ?>
    </div>

    <div class="border-t border-zinc-100 pt-6 space-y-6">
      
      <?php if (!empty($opp['summary'])): ?>
      <div class="text-sm text-zinc-700 leading-relaxed font-medium bg-zinc-50 p-4 rounded-xl border border-zinc-100">
        <?= htmlspecialchars($opp['summary']) ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($opp['description'])): ?>
      <div>
        <h2 class="text-sm font-bold text-zinc-950 mb-2">عن الفرصة</h2>
        <div class="text-xs sm:text-sm text-zinc-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['description']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($opp['benefits'])): ?>
      <div class="pt-4 border-t border-zinc-100">
        <h2 class="text-sm font-bold text-zinc-950 mb-2">المميزات والتمويل</h2>
        <div class="text-xs sm:text-sm text-zinc-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['benefits']) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($opp['requirements'])): ?>
      <div class="pt-4 border-t border-zinc-100">
        <h2 class="text-sm font-bold text-zinc-950 mb-2">الشروط والمتطلبات</h2>
        <div class="text-xs sm:text-sm text-zinc-600 leading-relaxed whitespace-pre-line">
          <?= htmlspecialchars($opp['requirements']) ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

  </main>

  <!-- زر التقديم المباشر أسفل الشاشة -->
  <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-zinc-200 p-3 sm:py-4 z-50">
    <div class="max-w-3xl mx-auto flex items-center gap-3">
      <a href="index.php" class="p-3 rounded-xl border border-zinc-200 text-zinc-600 hover:bg-zinc-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      </a>
      <a href="<?= htmlspecialchars($opp['apply_link']) ?>" target="_blank" rel="noopener noreferrer" class="flex-1 py-3 px-4 bg-zinc-950 hover:bg-zinc-800 active:scale-95 text-white font-semibold text-xs sm:text-sm text-center rounded-xl transition">
        التقديم عبر الموقع الرسمي المباشر ↗
      </a>
    </div>
  </div>

</body>
</html>
