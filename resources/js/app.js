import './echo';

const userId = document.querySelector('meta[name="user-id"]')?.content;

function showToast(message) {
    let container = document.querySelector('#notification-toasts');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-toasts';
        container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:1050;min-width:300px;max-width:360px;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'alert alert-primary alert-dismissible fade show shadow';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = '<i class="fas fa-bell me-2"></i>' + message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 6000);
}

function updateUnreadBadge() {
    const badge = document.querySelector('#notification-badge');
    if (!badge) return;
    const current = parseInt(badge.textContent, 10) || 0;
    badge.textContent = current + 1;
    badge.classList.remove('d-none');
}

if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`)
        .listen('.BroadcastNotificationCreated', (e) => {
            showToast(e.data.message || (window.AppMessages?.newNotification ?? 'لديك إشعار جديد'));
            updateUnreadBadge();
            prependNotification(e.data);
        });
}

function prependNotification(data) {
    const list = document.querySelector('#notifications-list');
    if (!list) return; // نحنا مو ع صفحة الإشعارات

    // أخفي حالة "لا توجد إشعارات" وأظهر البطاقة
    const empty = document.querySelector('#empty-notifications');
    const card = document.querySelector('#notifications-card');
    if (empty) empty.classList.add('d-none');
    if (card) card.classList.remove('d-none');

    const el = document.createElement('div');
    el.className = 'list-group-item bg-light';
    const t = window.AppMessages ?? {};
    el.innerHTML = `
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary rounded-pill">${t.newBadge ?? 'جديد'}</span>
            <small class="text-muted"><i class="fas fa-clock me-1"></i>${t.now ?? 'الآن'}</small>
        </div>
        <p class="mb-1 fw-semibold">${data.message}</p>
        <a href="/tasks/${data.task_id}" class="btn btn-sm btn-outline-primary mt-1">
            <i class="fas fa-eye me-1"></i>${t.viewTask ?? 'عرض المهمة'}
        </a>`;
    list.prepend(el);
}
