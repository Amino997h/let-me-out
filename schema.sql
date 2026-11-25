-- سكربت إنشاء جداول قاعدة بيانات منصة Let Me Out (LTO)

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `opportunities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `country` VARCHAR(100) NOT NULL,
    `country_code` VARCHAR(10) DEFAULT '🌍',
    `category` ENUM('immigration', 'scholarship', 'job', 'volunteering', 'nomad', 'internship', 'other') NOT NULL DEFAULT 'other',
    `funding_type` ENUM('fully_funded', 'partially_funded', 'self_funded', 'paid_job') NOT NULL DEFAULT 'fully_funded',
    `summary` TEXT NOT NULL,
    `description` LONGTEXT NOT NULL,
    `requirements` TEXT NULL,
    `benefits` TEXT NULL,
    `apply_link` VARCHAR(500) NOT NULL,
    `deadline` DATE NULL,
    `views` INT DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `status` ENUM('active', 'expired', 'draft') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إضافة حساب أدمن افتراضي: admin / admin123 (كلمة المرور مشفرة عبر bcrypt)
INSERT IGNORE INTO `admins` (`id`, `username`, `password`) 
VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- إدراج نماذج أولية تجريبية للفرص
INSERT INTO `opportunities` (`title`, `slug`, `country`, `country_code`, `category`, `funding_type`, `summary`, `description`, `requirements`, `benefits`, `apply_link`, `deadline`, `is_featured`, `status`) 
VALUES 
('برنامج الهجرة العشوائية لأمريكا (اللوري DV)', 'dv-lottery-usa', 'الولايات المتحدة الأمريكية', '🇺🇸', 'immigration', 'fully_funded', 'الفرصة السنوية الأكبر للحصول على الجرين كارد والإقامة الدائمة الأمريكية مجاناً.', 'القرعة الأمريكية الرسمية السنوية توفر 55 ألف تأشيرة إقامة دائمة لجميع المؤهلين حول العالم بدون شروط لغة أو حساب بنكي معقد.', '1. شهادة الثانوية العامة كحد أدنى أو خبرة عمل سنتين\n2. صورة شخصية بالمواصفات المعتمدة\n3. جواز سفر ساري', '1. إقامة دائمة (Green Card)\n2. تصريح عمل فوري في أمريكا\n3. إمكانية الحصول على الجنسية بعد 5 سنوات', 'https://dvprogram.state.gov/', '2026-11-05', 1, 'active'),

('منحة الحكومة التركية Türkiye Bursları', 'turkey-scholarship', 'تركيا', '🇹🇷', 'scholarship', 'fully_funded', 'منحة شاملة لكافة المراحل الدراسية (بكالوريوس، ماجستير، دكتوراه) مع سنة تحضيرية للغة.', 'من أضخم المنح الطلابية في العالم التي تغطي كافة المصاريف وتوفر فرص دراسة في أرقى الجامعات التركية.', '1. معدل تراكمي لا يقل عن 70% للبكالوريوس و 75% للدراسات العليا\n2. عدم تجاوز السن القانوني لكل مرحلة', '1. تغطية الرسوم الدراسية بنسبة 100%\n2. راتب شهري مجزي\n3. تذاكر الطيران ذهاباً وإياباً\n4. سكن جامعي مجاني\n5. تأمين صحي شامل', 'https://tbbs.turkiyeburslari.gov.tr/', '2026-02-20', 1, 'active'),

('فرصة تطوع ممولة بالكامل في ألمانيا مع الاتحاد الأوروبي', 'esc-volunteering-germany', 'ألمانيا', '🇩🇪', 'volunteering', 'fully_funded', 'فرصة تطوع ضمن برنامج European Solidarity Corps لمدة سنة في برلين.', 'برنامج تطوعي رائع يمنح الشباب فرصة خوض تجربة العمل الاجتماعي والثقافي في ألمانيا مع توفير كافة الاحتياجات الحياتية.', '1. العمر بين 18 و 30 سنة\n2. معرفة أساسية باللغة الإنجليزية أو الألمانية\n3. الرغبة والدافع للتطوع', '1. سكن مجهز بالكامل\n2. مصروف جيب وبدل طعام شهري\n3. تذاكر السفر وتأمين صحي\n4. دورة لغة ألمانية مجانية', 'https://youth.europa.eu/solidarity_en', '2026-05-30', 0, 'active');
