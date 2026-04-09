# Sports Voting Management Platform
# منصة التصويت الرياضي

## نظام تصويت مركزي للجمعيات الرياضية

نظام إنتاجي كامل لإدارة عمليات التصويت بهيكل هرمي (جمعية - أندية - لاعبين).

---

## المتطلبات

- PHP 8.3+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ و npm

## التقنيات المستخدمة

- **Laravel 11** - الإطار الأساسي
- **Laravel Breeze** - المصادقة
- **Spatie Laravel Permission** - إدارة الأدوار والصلاحيات
- **Maatwebsite Excel** - استيراد/تصدير Excel/CSV
- **Barryvdh DomPDF** - تصدير PDF
- **Bootstrap 5 RTL** - واجهة المستخدم (عربي)
- **Bootstrap Icons** - الأيقونات

---

## التثبيت والتشغيل

### 1. تثبيت الحزم

```bash
composer install
npm install && npm run build
```

### 2. إعداد قاعدة البيانات

أنشئ قاعدة بيانات MySQL باسم `sports_voting`:

```sql
CREATE DATABASE sports_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. ضبط ملف .env

تأكد من ضبط بيانات قاعدة البيانات في `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sports_voting
DB_USERNAME=root
DB_PASSWORD=
```

### 4. تنفيذ المايجريشن والبيانات التجريبية

```bash
php artisan migrate --seed
```

### 5. تشغيل السيرفر

```bash
php artisan serve
```

ثم افتح: **http://localhost:8000**

---

## الحسابات التجريبية

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| مدير الجمعية (Super Admin) | `admin@sportsvoting.com` | `password` |
| إداري نادي الهلال | `admin1@club.com` | `password` |
| إداري نادي النصر | `admin2@club.com` | `password` |
| إداري نادي الأهلي | `admin3@club.com` | `password` |
| إداري نادي الاتحاد | `admin4@club.com` | `password` |

---

## البيانات التجريبية

- **4 أندية**: الهلال، النصر، الأهلي، الاتحاد
- **4 إداريين**: إداري لكل نادي
- **40 لاعب**: 10 لاعبين لكل نادي
- **حملة تصويت نشطة**: تصويت أفضل لاعب للموسم 2025-2026
- **3 أسئلة**: اختيار واحد + اختيار متعدد + سؤال اختياري
- **تعيينات**: الحملة مرسلة لجميع إداريي الأندية

---

## الهيكل الإداري

### Super Admin (الجمعية) - `/admin/*`
- لوحة تحكم شاملة مع إحصائيات
- إدارة الأندية (CRUD + تفعيل/تعطيل)
- إدارة الإداريين (CRUD + إعادة تعيين كلمة المرور)
- عرض جميع اللاعبين مع بحث وفلترة
- إنشاء حملات التصويت الديناميكية
- بناء الأسئلة (Radio, Checkbox) مع خيارات غير محدودة
- إرسال الحملات للإداريين
- تتبع حالة الإرسال والفتح والتصويت
- عرض النتائج مع رسوم بيانية
- تصدير التقارير (CSV, PDF)
- سجل العمليات والتدقيق

### Club Admin (إداري النادي) - `/club/*`
- لوحة تحكم خاصة بالنادي
- إدارة لاعبي النادي (CRUD)
- استيراد اللاعبين من Excel/CSV
- استقبال حملات التصويت
- توليد روابط تصويت فريدة للاعبين
- نسخ وتوزيع الروابط
- متابعة حالة التصويت
- عرض نتائج النادي فقط

### Player (اللاعب) - `/vote/{token}`
- فتح رابط تصويت فريد
- واجهة تصويت متجاوبة (Mobile-first)
- مراجعة الإجابات قبل الإرسال
- شاشة نجاح بعد التصويت
- منع التصويت المتكرر

---

## سير العمل

```
1. Super Admin ينشئ نادي
2. Super Admin ينشئ إداري للنادي
3. Club Admin يضيف لاعبين (يدوياً أو استيراد)
4. Super Admin ينشئ حملة تصويت مع أسئلة
5. Super Admin يرسل الحملة للإداريين
6. Club Admin يستقبل الحملة
7. Club Admin يولد روابط تصويت
8. Club Admin يوزع الروابط على اللاعبين
9. اللاعب يفتح الرابط ويصوت
10. يمكن متابعة النتائج والإحصائيات
```

---

## هيكل قاعدة البيانات

| الجدول | الوصف |
|--------|-------|
| `users` | المستخدمون (Super Admin + Club Admin) |
| `roles` / `permissions` | الأدوار والصلاحيات (Spatie) |
| `clubs` | الأندية |
| `players` | اللاعبون |
| `voting_campaigns` | حملات التصويت |
| `voting_campaign_targets` | الأندية المستهدفة لكل حملة |
| `voting_questions` | أسئلة التصويت |
| `voting_question_options` | خيارات الإجابة |
| `campaign_assignments` | تعيينات الحملة للإداريين |
| `voting_links` | روابط التصويت الفريدة |
| `voting_sessions` | جلسات التصويت |
| `voting_responses` | استجابات التصويت |
| `voting_response_answers` | إجابات التصويت التفصيلية |
| `activity_logs` | سجل العمليات والتدقيق |
| `imports` | سجل عمليات الاستيراد |
| `notifications` | الإشعارات |

---

## الأمان

- RBAC كامل باستخدام Spatie Laravel Permission
- روابط تصويت فريدة (64 حرف عشوائي)
- كل رابط صالح لمرة واحدة فقط
- CSRF Protection على جميع النماذج
- Soft Deletes للبيانات الحساسة
- عزل بيانات الأندية (Club Isolation)
- سجل تدقيق شامل (Audit Log)
- تسجيل IP و User Agent لكل تصويت

---

## التقارير المتاحة

- تقرير نتائج الحملة (CSV)
- تقرير نتائج الحملة (PDF)
- تقرير المشاركة والمصوتين (CSV)
- إحصائيات حسب النادي
- إحصائيات حسب السؤال

---

## أوامر مفيدة

```bash
# تشغيل المايجريشن
php artisan migrate

# تشغيل البيانات التجريبية
php artisan db:seed

# إعادة تعيين القاعدة
php artisan migrate:fresh --seed

# تشغيل السيرفر
php artisan serve

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## هيكل المجلدات الرئيسية

```
app/
├── Http/Controllers/
│   ├── Admin/          # Super Admin Controllers
│   ├── Club/           # Club Admin Controllers
│   └── Voting/         # Public Voting Controller
├── Models/             # Eloquent Models
├── Services/           # Business Logic
├── Imports/            # Excel Import Classes
└── Http/Middleware/     # Custom Middleware

resources/views/
├── admin/              # Super Admin Views
├── club/               # Club Admin Views
├── voting/             # Public Voting Portal
└── layouts/            # Layout Templates

database/migrations/    # Database Migrations
database/seeders/       # Demo Data Seeders
routes/web.php          # Web Routes
routes/api.php          # API Routes
```
