# مفاهيم للتعلم

## 1. Broadcastings / Real-Time (Realtime Notifications)

**الفكرة:** بدل ما المستخدم يعمل refresh للصفحة ليشوف الإشعارات، السيرفر يدفع له الأحداث لحظة حدوثها عبر WebSocket.

**المكونات:**
- **Event** : يحط رسالة عالقناة (في مشروعنا: `app/Events/NotificationSent.php`)
- **Reverb** : سيرفر WebSocket محلي (تقليديًا Pusher) — config بالـ `.env`
- **Laravel Echo (frontend)** : العميل اللي بيلتقط الرسالة ويستمع للقناة (`resources/js/echo.js` + `app.js`)

**وين بيظهر بالمشروع:** لما يتم إسناد مهمة، الحدث `NotificationSent::dispatch(...)` بيبعت إشعار حي للمستخدم المحدد عالقناة `App.Models.User.{id}`، والـ frontend بيضيف toast فوري بدون تحديث الصفحة.

**نقاط تتعلمها:** قناة عامة vs خاصة (`PublicChannel` vs `PrivateChannel`)، `broadcastOn()`, `broadcastAs()`, `ShouldBroadcastNow`.

---

## 2. Authorization: Gates & Policies (الصلاحيات)

**الفكرة:** فكرة فهم من المسموح ينفذ شو. لو مشروعك لك، هل تعمل عليه تعديل أو تحذيف؟ الـ Policies بتفصل منطق الصلاحيات بشكل نظيف ومرتب.

**المكونات:**
- **Policy** : كلاس مرتبط بـ Model (مثل `ProjectPolicy`)
- **Gate** : أداة التفعيل — `Gate::denies('update', $project)`

**وين بيظهر بالمشروع:** `ProjectPolicy` و `TaskPolicy` — مالك المشروع فقط يقدر يشوف/يعدل/يحذف، وتنفذ عبر `Gate::denies(...)` بكل الـ Controllers.

**نقاط تتعلمها:** الفرق بين `denies` و `allows`, الربط التلقائي بالاسم `ModelPolicy`, استخدامها داخل Blade بـ `@can`.

---

## 3. Eloquent Relationships & Eager Loading (مشكلة N+1)

**الفكرة:** لما تجيب مشاريع كثيرة، كل علاقة بينم الوالد عليها بيطلع query إضافية. Eager Loading بيجيب العلاقات بـ query واحد (أو قليل) مع `with()` ويوفر أداء كبير.

**المشكلة N+1:** لو 100 مشروع، الاستعلام بدون `with('tasks')` بيجيب 101 query بدل 1.

**المكونات:**
- **Relationships** : `hasMany`, `belongsTo` (مثل `Project::tasks()`, `Task::assignee()`)
- **Eager Loading** : `->with('assignee')` أو `->loadCount('tasks')` ليجيب العدادات بجملة سريعة
- `TaskResource` و `ProjectResource` بتحدد شكل البيانات المخرجة للـ API

**وين بيظهر بالمشروع:** كله بالمشروع — `ProjectController::index` بـ `withCount('tasks')`, `TaskController` بـ `with('assignee', 'comments')`.

**نقاط تتعلمها:** الفرق بين `with()` و `load()`، `withCount()`, `latest()`, والفعالية الزمنية بشكل عام.

---

---

## 4. Form Requests & Validation (تحقق المدخلات)

**الفكرة:** بدل ما تكتب تحقق لكل إدخال بالـ Controller (صفحته بتصير طويلة)، بتعمل كلاس متخصص لكل حالة ورقة، المسؤول عن التحقق من البيانات اللي جاية من المستخدم.

**المكونات:**
- **Form Request** : كلاس بيحتوي على `rules()` (قواعد التحقق) و `authorize()` (صلاحية التنفيذ)
- ميزة: نفس القواعد بتشتغل للـ web وللـ API مع بعض

**وين بيظهر بالمشروع:** مجلد `app/Http/Requests/` مليان — `StoreTaskRequest`, `StoreProjectRequest`, `StoreUserRequest`, `UpdateStatusRequest`… وكلها بتفوض بالـ Controllers.

**نقاط تتعلمها:** الفرق بين `validate()` المدمج و Form Request، قواعد شائعة (required, email, unique, exists), رسائل مخصصة.

---

## 5. API Resources & Traits (تشكيل الردود)

**الفكرة:** مش دايماً بدك ترسل كل حقول الـ Model للمستخدم. الـ Resource بحدد شكل الرد بدقة (أي حقول، مالتقى، وبأي صيغة). والـ Trait بيسمح بتكرار كود مشترك بأماكن كثيرة.

**المكونات:**
- **Resource** : كلاس بـ `toArray()` بيشكل الرد (مثل `TaskResource`)
- **Trait** : مجموعة دوال جاهزة بمزجها بالكلاس (مثل `ApiResponseTrait`)
- `success()` و `error()` بتوحيد شكل كل الردود JSON

**وين بيظهر بالمشروع:** `app/Http/Resources/` فيه 4 Resources، وكل الـ Controllers بتستخدم `ApiResponseTrait` فبيطلع كل الردود بنفس الصيغة.

**نقاط تتعلمها:** `Resource::collection()`, `whenLoaded()`, وكيفية توحيد الردود فترتاح بالـ frontend.

---

## 6. Sanctum & API Authentication (مصادقة الـ API)

**الفكرة:** الـ web بيشتغل بالـ session، بس التطبيقات أو الـ mobile بيحتاجو token (رمز) يحملو مع كل طلب. Sanctum بيوفر Token-based auth بسيط.

**المكونات:**
- **Sanctum** : بيوفر `createToken() > plainTextToken`
- **Middleware** : `auth:sanctum` بحمي الـ API routes
- الـ client بيبعت الـ token برأس `Authorization: Bearer`

**وين بيظهر بالمشروع:** `routes/api.php` — `/register` و `/login` بترجع token، وكل باقي النقاط محمية بـ `auth:sanctum`. الـ logout بيمسح الـ token الحالي.

**نقاط تتعلمها:** الفرق بين session و token، جدول `personal_access_tokens`, طريقة حماية الـ routes.

---

### سير التعلم المقترح:
1. ابدأ من **Eloquent Relationships** لأنه أساسي ومن ضمنه بتفهم بنية المشروع.
2. بعده **Policies** لأنه بيحسن أمان تطبيقك.
3. وبعده **Form Requests + Resources** — بيفرق (Request) ع (Response) وبيرتب كودك.
4. وهذا **Sanctum** و **Broadcasting** — الأصعب والأكتر إثارة.

---

# مفاهيم جديدة (ما زال ما منفذتها بالمشروع) — اتعلمها وضيفها

> هدول 3 مفاهيم **مش موجودة** بمشروعك حاليًا. فكرة التعلم: كل مفهوم لازم تضيفه للمشروع بنفسك وتشوفه شغال.

## 7. Model Observers (مراقبو النماذج) — الأسهل

**القصة:** حاليًا منطق "إذا اكتملت كل المهام، خلي المشروع completed" مكتوب داخل الـ Controller (كود مكرر). الـ Observer بيسمحلك تسمع لعمليات الـ Model (created, updated…) وتنفذ رد فعل تلقائي — خارج الـ Controller.

**شو بتضيف للمشروع:** `app/Observers/TaskObserver.php` — لما تتحدث حالة المهمة `updated`، هو بيحسب حالة المشروع تلقائيًا. وبتسجلو بـ `AppServiceProvider` (لاحظ: هلق `AppServiceProvider` فاضي!).
بعد هيك بتحذف كود التحديث من `TaskController::updateStatus` لأن المسؤولية انتقلت.

**نقاط تتعلمها:** الفرق بين Model Events و Observers، تسجيل الـ Observer بـ `boot()`, متى تروح عليه ومتى لا.

## 8. Queues & Queueable Jobs (المهام الخلفية) — المتوسط

**القصة:** إرسال الإشعار و الـ broadcast هلق بتتنفذ **بتزامن مع الطلب** — يعني الاستجابة بتتأخر شوي. الـ Queue بتخلي العملية تنفذ بالخلفية وترجع الزبون بأسرع وقت. (مشروعك جاهز لها: `QUEUE_CONNECTION=database` موجود!)

**شو بتضيف للمشروع:** تخلي إشعارات الـ assign تروح على الـ queue — مثلاً `TaskAssignedNotification` يصير `ShouldQueue`، أو تسوي Job خاصة بالتذكير: `SendDueDateReminders` يفحص كل صباح على المهام اللي تاريخها قرب يخلص.

**نقاط تتعلمها:** `ShouldQueue`, تشغيل الـ worker (`php artisan queue:work`), الـ Job كمفهوم، `withDelay()` للتأخير.

## 9. Testing with Pest (اختبارات فعلية) — الأهم للمستقبل

**القصة:** مشروعك فيه ملفات `tests/` بس كلها أمثلة (`ExampleTest`). الـ tests بتخللي أي تغيير تعمله بالكود ما يكسر الشغل الموجود، وبتعلّمك تكتب كود آمن.

**شو بتضيف للمشروع:** Feature Tests بالـ Pest، مثلاً:
- `tests/Feature/ProjectTest.php` → "المستخدم يقدر يسجل ويشوف مشاريعه بس"
- `tests/Feature/TaskTest.php` → "فلان ما يقدر يعدل مهمة بمشروع مو له"
- `tests/Feature/AuthTest.php` → "التسجيل والدخول والخروج"

**نقاط تتعلمها:** `pest()` vs `test()`, `actingAs()`, `assertDatabaseHas()`, `refreshDatabase()`, وتشغيلها بـ `php artisan test`.

---

### خطة تطبيق هالثلاثة بالترتيب:
1. **Observer** (أسهل، بيرتب كود موجود)
2. **Queue** (بتوصل لمرحلة كيف تكون الأبليكشن سريع)
3. **Testing** (تبني الحماية لتعديلاتك الجاية)