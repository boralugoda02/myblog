{{-- Light / dark mode. Include ONCE inside <head>. --}}
<script>
    
    (function () {
        var saved = null;
        try { saved = localStorage.getItem('theme'); } catch (e) {}
        document.documentElement.setAttribute('data-bs-theme', saved === 'dark' ? 'dark' : 'light');
    })();

    function updateThemeButton() {
        var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        var icon = document.getElementById('themeIcon');
        var text = document.getElementById('themeText');

        if (icon) {
            if (icon.classList.contains('bi')) {
                icon.className = 'bi ' + (dark ? 'bi-sun-fill' : 'bi-moon-stars-fill');
            } else {
                icon.textContent = dark ? '☀️' : '🌙';
            }
        }
        if (text) {
            text.textContent = dark ? 'Light' : 'Dark';
        }
    }

    function toggleTheme() {
        var next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
        updateThemeButton();
    }

    document.addEventListener('DOMContentLoaded', updateThemeButton);
</script>

<style>
    
    html[data-bs-theme="dark"] body { background-color: #0b1220 !important; color: #e2e8f0 !important; }

    html[data-bs-theme="dark"] .bg-white,
    html[data-bs-theme="dark"] .card,
    html[data-bs-theme="dark"] .card-custom { background-color: #111c33 !important; border-color: #24324f !important; color: #e2e8f0 !important; }

    html[data-bs-theme="dark"] .bg-light { background-color: #16233f !important; }
    html[data-bs-theme="dark"] .text-dark { color: #e2e8f0 !important; }
    html[data-bs-theme="dark"] .text-muted,
    html[data-bs-theme="dark"] .text-secondary { color: #94a3b8 !important; }
    html[data-bs-theme="dark"] .border,
    html[data-bs-theme="dark"] .border-bottom,
    html[data-bs-theme="dark"] .border-top { border-color: #24324f !important; }

    html[data-bs-theme="dark"] .list-group-item { background-color: #111c33; color: #e2e8f0; border-color: #24324f; }
    html[data-bs-theme="dark"] .table { --bs-table-bg: #111c33; --bs-table-color: #e2e8f0; --bs-table-border-color: #24324f; --bs-table-hover-bg: #16233f; --bs-table-hover-color: #fff; }
    html[data-bs-theme="dark"] .table-light { --bs-table-bg: #16233f; --bs-table-color: #cbd5e1; --bs-table-border-color: #24324f; }
    html[data-bs-theme="dark"] .modal-content { background-color: #111c33; color: #e2e8f0; }
    html[data-bs-theme="dark"] .dropdown-menu { background-color: #111c33; border-color: #24324f; }
    html[data-bs-theme="dark"] .btn-light { background-color: #1b2a4a; border-color: #24324f; color: #e2e8f0; }

    
    html[data-bs-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
    html[data-bs-theme="dark"] select,
    html[data-bs-theme="dark"] textarea:not(#editor) { background-color: #0f1a30 !important; color: #e2e8f0 !important; border-color: #2b3b5e !important; }

    
    html[data-bs-theme="dark"] [style*="background: #ffffff"] { background-color: #111c33 !important; }
    html[data-bs-theme="dark"] [style*="background: #f9fafb"] { background-color: #16233f !important; }
    html[data-bs-theme="dark"] [style*="color: #111827"],
    html[data-bs-theme="dark"] [style*="color: #374151"] { color: #e2e8f0 !important; }
    html[data-bs-theme="dark"] [style*="border: 1px solid #e5e7eb"] { border-color: #24324f !important; }

    
    html[data-bs-theme="dark"] .post-body h1,
    html[data-bs-theme="dark"] .post-body h2,
    html[data-bs-theme="dark"] .post-body h3,
    html[data-bs-theme="dark"] .post-body h4 { color: #f1f5f9; }
    html[data-bs-theme="dark"] .post-body th { background: #16233f; }
    html[data-bs-theme="dark"] .post-body th,
    html[data-bs-theme="dark"] .post-body td { border-color: #24324f; }
</style>