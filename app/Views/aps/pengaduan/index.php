<?= $this->extend($role . '/layout') ?>
<?= $this->section('content') ?>

<?php
$userRole = session()->get('role');
$canUpdateStatus = in_array($userRole, ['sudo', 'monitoring']);
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary: #0d6efd;
        --primary-light: #e7f1ff;
        --primary-dark: #0b5ed7;
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #0dcaf0;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-600: #475569;
        --gray-700: #334155;
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f1ff 100%);
        /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
        min-height: 100vh;
        color: var(--gray-700);
    }

    .container {
        max-width: 1200px;
    }

    /* ===== HEADER SECTION ===== */
    .header-section {
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 10px 40px rgba(13, 110, 253, 0.15);
        position: relative;
        overflow: hidden;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-subtitle {
        opacity: 0.9;
        font-size: 0.95rem;
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--primary);
        transition: var(--transition);
    }

    .filter-section:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .filter-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .filter-title i {
        color: var(--primary);
    }

    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control,
    .form-select {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: var(--transition);
        background-color: var(--gray-50);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        transition: var(--transition);
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
    }

    .btn-light {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1.5px solid var(--gray-200);
    }

    .btn-light:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
    }

    .result-count {
        font-size: 0.9rem;
        color: var(--gray-600);
        margin-top: 0.75rem;
        font-weight: 500;
    }

    /* ===== CARD PENGADUAN ===== */

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pengaduan-card:hover {
        border-color: var(--primary);
        box-shadow: 0 8px 30px rgba(13, 110, 253, 0.15);
        transform: translateY(-4px);
    }

    .pengaduan-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* ===== REPLY SECTION ===== */

    /* ===== FORM REPLY ===== */
    .form-reply {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed rgba(148, 163, 184, 0.45);
    }

    .form-reply textarea {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-family: inherit;
        font-size: 0.95rem;
        resize: none;
        min-height: 80px;
        max-height: 200px;
        overflow-y: auto;
        transition: var(--transition);
        flex: 1;
        background: rgba(248, 250, 252, 0.9);
        border-color: rgba(148, 163, 184, 0.7);
        font-size: 0.9rem;
    }

    .form-reply textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    .form-reply button {
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, var(--info) 0%, #0dbcf0 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        align-self: flex-end;
        box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
    }

    .form-reply button i {
        font-size: 1rem;
    }

    .form-reply button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 202, 240, 0.4);
    }

    /* ===== MODAL ===== */
    .modal-content {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        color: white;
        border: none;
        border-radius: var(--radius) var(--radius) 0 0;
        padding: 1.75rem;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 1.75rem;
    }

    .modal-footer {
        border-top: 1.5px solid var(--gray-200);
        padding: 1.25rem;
        gap: 1rem;
    }

    .alert-info {
        background: linear-gradient(135deg, #e7f1ff 0%, #f0f7ff 100%);
        border: 1.5px solid rgba(13, 110, 253, 0.2);
        color: var(--primary);
        border-radius: 8px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: var(--radius);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 3.5rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: var(--gray-600);
        font-size: 1.05rem;
    }

    /* TEXTAREA AUTO RESIZE + SCROLL LIMIT */
    textarea.auto-resize {
        resize: none;
        overflow-y: auto;
        min-height: 80px;
        max-height: 220px;
        /* batas tinggi */
        transition: height .2s ease;
    }

    /* ===== CARD PENGADUAN (UTAMA) ===== */
    .pengaduan-card {
        position: relative;
        background: linear-gradient(145deg, #ffffff 0%, #f9fbff 100%);
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        padding: 1.5rem 1.5rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow:
            0 18px 45px rgba(15, 23, 42, 0.12),
            0 0 0 1px rgba(148, 163, 184, 0.15);
        transition: var(--transition);
        overflow: hidden;
    }

    .pengaduan-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left,
                rgba(56, 189, 248, 0.15),
                transparent 60%);
        opacity: 0;
        pointer-events: none;
        transition: var(--transition);
    }

    .pengaduan-card:hover {
        transform: translateY(-4px);
        box-shadow:
            0 22px 60px rgba(15, 23, 42, 0.16),
            0 0 0 1px rgba(59, 130, 246, 0.2);
    }

    .pengaduan-card:hover::before {
        opacity: 1;
    }

    /* header atas: avatar + nama + badge + waktu + tombol pdf */
    .pengaduan-header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.5);
    }

    .pengaduan-sender {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .sender-avatar {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: conic-gradient(from 210deg, #0ea5e9, #6366f1, #0ea5e9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    }

    .sender-info h6 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 1rem;
    }

    .sender-info .badge {
        background: rgba(15, 23, 42, 0.9);
        padding: 0.25rem 0.6rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 999px;
        color: #e5e7eb;
    }

    /* meta kanan: waktu + PDF button */
    .pengaduan-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pengaduan-time {
        font-size: 0.8rem;
        color: var(--gray-600);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.03);
    }

    .pengaduan-time i {
        font-size: 0.9rem;
        color: #0ea5e9;
    }

    /* tombol pdf */
    .btn-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 10px 22px rgba(239, 68, 68, 0.3);
        transition: var(--transition);
    }

    .btn-pdf i {
        font-size: 0.85rem;
    }

    .btn-pdf:hover {
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 14px 30px rgba(239, 68, 68, 0.4);
    }

    /* isi aduan (body utama) */
    .pengaduan-content {
        position: relative;
        background: rgba(248, 250, 252, 0.95);
        padding: 1.1rem 1.1rem 1rem;
        border-radius: 14px;
        margin: 0.75rem 0 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        line-height: 1.65;
        color: var(--gray-700);
        font-size: 0.92rem;
    }

    .pengaduan-content::before {
        /* content: 'Aduan'; */
        content: attr(data-subject);
        position: absolute;
        top: -0.9rem;
        left: 1rem;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: #0f172a;
        color: #e5e7eb;
        padding: 0.1rem 0.55rem;
        border-radius: 999px;
    }

    .pengaduan-divider {
        margin: 0.9rem 0 0.8rem;
        border: 0;
        border-top: 1px dashed rgba(148, 163, 184, 0.6);
    }

    /* ===== REPLY SECTION (BALASAN) ===== */
    /* Container list balasan */
    .replies-container {
        margin: 0.25rem 0 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    /* Row untuk atur kiri-kanan */
    .reply-row {
        display: flex;
        width: 100%;
    }

    /* balasan orang lain (kiri) */
    .reply-row-other {
        justify-content: flex-start;
    }

    /* balasan saya (kanan) */
    .reply-row-me {
        justify-content: flex-end;
    }

    /* Bubble dasar */
    .reply-item {
        max-width: 80%;
        position: relative;
        padding: 0.6rem 0.9rem 0.5rem;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
        border: 1px solid transparent;
    }

    /* bubble orang lain (kiri) */
    .reply-other {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-color: rgba(148, 163, 184, 0.6);
        border-bottom-left-radius: 4px;
        /* sudut kiri bawah lebih tajam (kayak ekor) */
    }

    /* bubble saya (kanan) */
    .reply-me {
        background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
        border-color: rgba(37, 99, 235, 0.8);
        color: #f9fafb;
        border-bottom-right-radius: 4px;
        /* sudut kanan bawah lebih tajam */
    }

    /* teks di bubble */
    .reply-me .reply-text {
        color: #eff6ff;
    }

    .reply-other .reply-text {
        color: #374151;
    }

    /* header author kecil */
    .reply-author {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 600;
        font-size: 0.8rem;
        margin-bottom: 0.1rem;
    }

    .reply-me .reply-author {
        color: #e0f2fe;
    }

    .reply-other .reply-author {
        color: #4f46e5;
    }

    /* icon reply */
    .reply-author i {
        font-size: 0.8rem;
    }

    /* teks isi */
    .reply-text {
        line-height: 1.5;
        font-size: 0.88rem;
        margin-bottom: 0.2rem;
    }

    /* waktu di bawah kanan */
    .reply-time {
        font-size: 0.72rem;
        opacity: 0.8;
        text-align: right;
    }

    /* warna waktu mengikuti bubble */
    .reply-me .reply-time {
        color: #dbeafe;
    }

    .reply-other .reply-time {
        color: #6b7280;
    }

    /* kalau belum ada balasan */
    .no-reply {
        text-align: center;
        padding: 0.9rem 0.75rem;
        color: #9ca3af;
        font-style: italic;
        font-size: 0.85rem;
        border-radius: 10px;
        border: 1px dashed rgba(148, 163, 184, 0.6);
        background: rgba(248, 250, 252, 0.8);
    }

    .quick-reply-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin: .25rem 0 .5rem;
    }

    .btn-quick-reply {
        border: 1px solid rgba(148, 163, 184, .55);
        background: rgba(248, 250, 252, .9);
        color: #334155;
        padding: .22rem .55rem;
        /* lebih kecil */
        font-size: .72rem;
        /* lebih kecil */
        border-radius: 999px;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        line-height: 1.2;
    }

    .btn-quick-reply i {
        font-size: .75rem;
        color: #0ea5e9;
    }

    /* SUBJECT */
    .complaint-subject {
        background: linear-gradient(135deg, var(--primary-light), rgba(219, 234, 254, 0.5));
        border-left: 3px solid var(--primary);
        padding: 1rem;
        border-radius: var(--radius-sm);
        margin-bottom: 1rem;
    }

    .complaint-subject-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .complaint-subject-text {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* container */
    .select2-container {
        width: 100% !important;
        font-size: 0.9rem;
    }

    /* single select */
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    /* arrow */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    /* rendered text */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0;
        color: #495057;
    }

    /* focus */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .25);
    }

    /* disabled */
    .select2-container--default .select2-selection--single[aria-disabled=true] {
        background-color: #e9ecef;
    }

    /* dropdown */
    .select2-dropdown {
        border-radius: 6px;
        border: 1px solid #ced4da;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    }

    /* search box */
    .select2-search--dropdown .select2-search__field {
        padding: 6px 10px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    /* options */
    .select2-results__option {
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    /* selected option */
    .select2-results__option--highlighted {
        background-color: #0d6efd;
        color: #fff;
    }

    /* clear button */
    .select2-selection__clear {
        color: #dc3545;
        margin-right: 6px;
        cursor: pointer;
    }

    /* =========================
    SELECT2 IN MODAL FIX
    ========================= */
    .modal .select2-container {
        z-index: 1056;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .header-title {
            font-size: 1.5rem;
        }

        .pengaduan-header {
            flex-direction: column;
        }

        .filter-section {
            padding: 1rem;
        }

        .form-reply {
            flex-direction: column;
        }

        .form-reply button {
            align-self: stretch;
        }
    }
</style>

<!-- Header -->
<div class="header-section">
    <div class="container">
        <div class="header-content">
            <div class="header-title">
                <i class="fas fa-comments"></i>
                Sistem Pengaduan &amp; Masukan
            </div>
            <div class="header-subtitle">
                Sampaikan keluhan atau masukan Anda dengan jelas agar tim kami bisa menindaklanjuti dengan cepat
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="fas fa-sliders-h"></i>
            Filter &amp; Pencarian
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label for="filterText" class="form-label">Cari Teks</label>
                <input id="filterText" type="text" class="form-control" placeholder="Isi atau balasan...">
            </div>

            <div class="col-6 col-md-3">
                <label for="filterRole" class="form-label">Tujuan</label>
                <select id="filterRole" class="form-select">
                    <option value="">Semua Bagian</option>
                    <option value="capacity">Capacity</option>
                    <option value="planning">PPC</option>
                    <option value="aps">Planner</option>
                    <option value="user">Area</option>
                    <option value="rosso">Rosso</option>
                    <option value="gbn">GBN</option>
                    <option value="celup">Celup Cones</option>
                    <option value="covering">Covering</option>
                    <option value="sudo">Monitoring Planning &amp; Produksi</option>
                    <option value="monitoring">Monitoring Bahan Baku</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label for="filterUser" class="form-label">Pengirim</label>
                <input id="filterUser" type="text" class="form-control" placeholder="Username...">
            </div>

            <div class="col-6 col-md-2">
                <label for="filterFrom" class="form-label">Dari</label>
                <input id="filterFrom" type="date" class="form-control">
            </div>

            <div class="col-6 col-md-2">
                <label for="filterTo" class="form-label">Sampai</label>
                <input id="filterTo" type="date" class="form-control">
            </div>
        </div>

        <div class="filter-actions">
            <button id="btnClearFilter" class="btn btn-light" type="button">
                <i class="fas fa-redo"></i> Reset
            </button>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan">
                <i class="fas fa-plus-circle"></i> Buat Pengaduan
            </button>
        </div>

        <div class="result-count">
            <i class="fas fa-list"></i> <span id="resultCount">0</span> hasil ditemukan
        </div>
    </div>

    <!-- Empty State -->
    <div
        class="empty-state"
        id="emptyState"
        style="<?= empty($threads) ? '' : 'display:none;' ?>">
        <i class="fas fa-inbox"></i>
        <p>Tidak ada pengaduan untuk ditampilkan</p>
    </div>

    <!-- FILTER STATUS -->
    <div class="mb-3 d-flex gap-2">
        <button class="btn btn-outline-secondary btn-filter-status" data-status="">
            Semua
        </button>
        <button class="btn btn-outline-warning btn-filter-status" data-status="in_progress">
            <i class="fas fa-spinner"></i> In Progress
        </button>
        <button class="btn btn-outline-success btn-filter-status" data-status="resolved">
            <i class="fas fa-circle-check"></i> Resolved
        </button>
        <button class="btn btn-outline-dark btn-filter-status" data-status="closed">
            <i class="fas fa-lock"></i> Closed
        </button>
    </div>


    <!-- List Pengaduan -->
    <div id="listPengaduan">
        <?php if (!empty($threads)): ?>
            <?php foreach ($threads as $thread): ?>
                <?php
                // Ambil pesan terakhir
                $last = end($thread['messages']);
                if (!$last) continue;

                $timestamp = strtotime($last['created_at']);
                $formattedTs = date('d M Y', $timestamp) . ' (' . date('H:i', $timestamp) . ')';
                $dateISO = date('Y-m-d', $timestamp);

                // Mapping role untuk display
                $roleMap = [
                    'sudo' => 'Monitoring',
                    'aps' => 'Planner',
                    'planning' => 'PPC',
                    'user' => 'Area',
                    'capacity' => 'Capacity',
                    'rosso' => 'Rosso',
                    'gbn' => 'GBN',
                    'celup' => 'Celup Cones',
                    'covering' => 'Covering',
                    'monitoring' => 'Monitoring Bahan Baku'
                ];

                $first = $thread['messages'][0] ?? null;
                $fromUsername = $first['username'] ?? '-';
                $fromRole = $first['role'] ?? '-';
                $fromDisplay = $roleMap[$fromRole] ?? $fromRole;

                $displayRole = $roleMap[$first['role']] ?? $first['role'];

                $searchTexts = [];

                foreach ($thread['messages'] as $m) {
                    $searchTexts[] = strtolower($m['username'] ?? '');
                    $searchTexts[] = strtolower($m['message'] ?? '');
                }

                $searchTexts[] = strtolower($displayRole);
                $searchTexts[] = strtolower($thread['subject'] ?? '');



                $searchBlob = implode(' ', $searchTexts);
                $username = $first['username'] ?? '';
                $avatarInitial = strtoupper(mb_substr($username, 0, 1));

                $toText = '-';

                if (!empty($thread['target_type'])) {
                    if ($thread['target_type'] === 'user') {
                        $toText = $thread['target_value']; // username
                    } elseif ($thread['target_type'] === 'role') {
                        $toText = $roleMap[$thread['target_value']] ?? $thread['target_value'];
                    }
                }

                $typeLabel = $thread['thread_type'] === 'direct'
                    ? 'Direct'
                    : 'Role';


                ?>

                <div class="pengaduan-card"
                    data-status="<?= esc($thread['status'] ?? 'open') ?>"
                    data-id="<?= $thread['id_thread'] ?>"
                    data-role="<?= esc($last['role'] ?? '') ?>"
                    data-user="<?= esc(strtolower($username)) ?>"
                    data-date="<?= esc($dateISO) ?>"
                    data-search="<?= esc($searchBlob) ?>">

                    <div class="pengaduan-header">
                        <div class="pengaduan-sender">
                            <div class="sender-avatar"><?= esc($avatarInitial) ?></div>
                            <div class="sender-info">
                                <h6>
                                    <?= esc($fromUsername) ?>
                                    <i class="fa-solid fa-angles-right"></i>
                                    <span class="badge"><?= esc($toText) ?></span>
                                    <!-- <small>(<?= $typeLabel ?>)</small> -->
                                </h6>
                            </div>
                        </div>
                        <div class="pengaduan-meta">
                            <div class="pengaduan-time">
                                <i class="fas fa-clock"></i>
                                <strong><?= esc($last['created_at']) ?></strong>
                            </div>

                            <?php
                            $status = $thread['status'] ?? 'in_progress';

                            $statusConfig = [
                                'open' => [
                                    'label' => 'Open',
                                    'icon'  => 'fa-unlock',
                                    'class' => 'bg-success'
                                ],
                                'in_progress' => [
                                    'label' => 'In Progress',
                                    'icon'  => 'fa-spinner',
                                    'class' => 'bg-warning'
                                ],
                                'resolved' => [
                                    'label' => 'Resolved',
                                    'icon'  => 'fa-circle-check',
                                    'class' => 'bg-success'
                                ],
                                'closed' => [
                                    'label' => 'Closed',
                                    'icon'  => 'fa-lock',
                                    'class' => 'bg-secondary'
                                ],
                            ];
                            ?>

                            <?php if ($canUpdateStatus): ?>
                                <!-- ROLE sudo & monitoring -->
                                <div class="dropdown">
                                    <button
                                        class="btn btn-sm dropdown-toggle <?= $statusConfig[$status]['class'] ?>"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="fas <?= $statusConfig[$status]['icon'] ?>"></i>
                                        <?= $statusConfig[$status]['label'] ?>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <?php foreach ($statusConfig as $key => $cfg): ?>
                                            <li>
                                                <a href="#"
                                                    class="dropdown-item btn-change-status <?= $key === 'closed' ? 'text-danger' : '' ?>"
                                                    data-id="<?= $thread['id_thread'] ?>"
                                                    data-status="<?= $key ?>">
                                                    <i class="fas <?= $cfg['icon'] ?>"></i>
                                                    <?= $cfg['label'] ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <!-- ROLE LAIN → badge doang -->
                                <span class="badge <?= $statusConfig[$status]['class'] ?>">
                                    <i class="fas <?= $statusConfig[$status]['icon'] ?>"></i>
                                    <?= $statusConfig[$status]['label'] ?>
                                </span>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php if (!empty($thread['subject'])): ?>
                        <div class="complaint-subject">
                            <div class="complaint-subject-label">Subject</div>
                            <div class="complaint-subject-text"><?= esc($thread['subject']) ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="replies-container">
                        <?php foreach ($thread['messages'] as $msg): ?>
                            <?php
                            $isMe = ($msg['sender_id'] == session()->get('id_user'));
                            ?>
                            <div class="reply-row <?= $isMe ? 'reply-row-me' : 'reply-row-other' ?>">
                                <div class="reply-item <?= $isMe ? 'reply-me' : 'reply-other' ?>">
                                    <div class="reply-author">
                                        <i class="fas fa-reply"></i>
                                        <?= esc($msg['username']) ?>
                                    </div>
                                    <div class="reply-text">
                                        <?= nl2br(esc($msg['message'])) ?>
                                    </div>
                                    <div class="reply-time">
                                        <?= esc($msg['created_at']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="pengaduan-divider">

                    <?php if (($thread['status'] ?? 'open') !== 'closed'): ?>
                        <form class="form-reply" data-id="<?= $thread['id_thread'] ?>">
                            <input type="hidden" name="id_user" value="<?= esc(session()->get('id_user')) ?>">
                            <textarea
                                id="reply-ta-<?= $thread['id_thread'] ?>"
                                name="isi"
                                class="auto-resize"
                                placeholder="Tulis balasan..."
                                required></textarea>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>

                        <div class="quick-reply-wrap" data-target="#reply-ta-<?= $thread['id_thread'] ?>">
                            <!-- Quick reply buttons -->
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="pengaduan-empty">
                Belum ada pengaduan
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Create Pengaduan -->
<div class="modal fade" id="modalCreatePengaduan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formCreate" class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-pen-fancy"></i> Buat Pengaduan Baru
                    </h5>
                    <small style="opacity: 0.9;">Sampaikan keluhan atau masukan Anda dengan jelas</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id_user" value="<?= esc(session()->get('id_user')) ?>">

                <div class="alert alert-info mb-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Tips:</strong> Jelaskan kronologi singkat, tanggal kejadian, dan detail penting lainnya.
                </div>

                <!-- BARIS 1 -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="target_role" class="form-label">
                            Ditujukan ke <span style="color: #dc3545;">*</span>
                        </label>
                        <select name="target_role" id="target_role" class="form-select">
                            <option value="">Pilih Role Tujuan</option>

                            <?php foreach ($roles['data'] as $r): ?>
                                <?php if (!$canUpdateStatus && $r['value'] === $userRole) continue; ?>
                                <option value="<?= esc($r['value']) ?>">
                                    <?= esc($r['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="user" class="form-label">Nama User</label>
                        <select id="user" name="user" class="form-select">
                            <option value="">Pilih User</option>
                        </select>
                    </div>
                </div>

                <!-- BARIS 2 -->
                <div class="mb-3">
                    <label for="subject" class="form-label">
                        Subject <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>

                <!-- BARIS 3 -->
                <div class="mb-3">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                        <label for="isi" class="form-label mb-0">
                            Isi Pengaduan <span style="color: #dc3545;">*</span>
                        </label>
                        <small style="color: var(--gray-600);" id="charCount">0 / 1000</small>
                    </div>
                    <textarea
                        id="isi"
                        name="isi"
                        class="form-control auto-resize"
                        rows="4"
                        maxlength="1000"
                        placeholder="Contoh: Tanggal xx/xx/2025 terjadi keterlambatan material..."
                        required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-send"></i> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== CHAR COUNTER MODAL =====
        const isiInput = document.getElementById('isi');
        const charCounter = document.getElementById('charCount');

        if (isiInput && charCounter) {
            const updateCounter = () => {
                charCounter.textContent = isiInput.value.length + ' / ' + (isiInput.maxLength || 1000);
            };
            isiInput.addEventListener('input', updateCounter);
            updateCounter();
        }

        // ===== AUTO RESIZE TEXTAREA =====
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';

            if (el.scrollHeight > el.clientHeight) {
                el.style.overflowY = 'auto';
            } else {
                el.style.overflowY = 'hidden';
            }
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('auto-resize')) {
                autoResize(e.target);
            }
        });

        function initAutoResize() {
            document.querySelectorAll('textarea.auto-resize').forEach(autoResize);
        }
        initAutoResize();
        window.initAutoResize = initAutoResize;

        // ===== FILTER LOGIC =====
        const filters = {
            text: document.getElementById('filterText'),
            role: document.getElementById('filterRole'),
            user: document.getElementById('filterUser'),
            from: document.getElementById('filterFrom'),
            to: document.getElementById('filterTo')
        };

        const resultCountEl = document.getElementById('resultCount');
        const emptyStateEl = document.getElementById('emptyState');
        const clearBtn = document.getElementById('btnClearFilter');

        function applyFilter() {
            const q = (filters.text.value || '').toLowerCase().trim();
            const role = (filters.role.value || '').trim();
            const user = (filters.user.value || '').toLowerCase().trim();
            const from = filters.from.value ? new Date(filters.from.value) : null;
            let to = filters.to.value ? new Date(filters.to.value) : null;

            if (to) to.setHours(23, 59, 59, 999);

            let visible = 0;
            document.querySelectorAll('.pengaduan-card').forEach(card => {
                const cardRole = (card.dataset.role || '').trim();
                const cardUser = (card.dataset.user || '').toLowerCase().trim();
                const cardDate = card.dataset.date ? new Date(card.dataset.date) : null;
                const search = (card.dataset.search || '').toLowerCase();

                const roleMatch = !role || role === cardRole;
                const userMatch = !user || cardUser.includes(user);
                const textMatch = !q || search.includes(q);
                let dateMatch = true;

                if (cardDate && (from || to)) {
                    if (from && cardDate < from) dateMatch = false;
                    if (to && cardDate > to) dateMatch = false;
                }

                const show = roleMatch && userMatch && textMatch && dateMatch;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (resultCountEl) resultCountEl.textContent = visible;
            if (emptyStateEl) emptyStateEl.style.display = visible === 0 ? 'block' : 'none';
        }

        window.applyFilter = applyFilter;

        Object.values(filters).forEach(filter => {
            if (filter) {
                filter.addEventListener('input', applyFilter);
                filter.addEventListener('change', applyFilter);
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                Object.values(filters).forEach(f => {
                    if (f) f.value = '';
                });
                applyFilter();
            });
        }

        applyFilter();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalCreatePengaduan');
        const userSelect = document.getElementById('user');
        const roleSelect = document.getElementById('target_role');

        modal.addEventListener('shown.bs.modal', function() {
            initUserSelect2();

            if (roleSelect.value) {
                loadUsersByRole(roleSelect.value);
            }
        });


        roleSelect.addEventListener('change', function() {
            if (this.value) {
                loadUsersByRole(this.value);
            } else {
                resetUserSelect();
            }
        });

        function initUserSelect2() {
            $('#user').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih User',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalCreatePengaduan')
            });
        }


        function resetUserSelect() {
            $('#user')
                .empty()
                .append('<option></option>')
                .val(null)
                .trigger('change')
                .prop('disabled', true);
        }

        function loadUsersByRole(role) {
            resetUserSelect();

            fetch(ComplaintUrl + 'getDataUserByRole', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ role: role })
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.success || !Array.isArray(res.data)) return;

                    res.data.forEach(user => {
                        const opt = new Option(user.username, user.username, false, false);
                        $('#user').append(opt);
                    });

                    $('#user')
                        .prop('disabled', false)
                        .trigger('change');
                })
                .catch(err => {
                    console.error('Gagal load user:', err);
                });
        }

        modal.addEventListener('hidden.bs.modal', function() {
            if ($('#user').hasClass('select2-hidden-accessible')) {
                $('#user').select2('destroy');
            }
        });

    });
</script>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-close-thread');
        if (!btn) return;

        const threadId = btn.dataset.id;

        Swal.fire({
            title: 'Tutup Pengaduan?',
            text: 'Yakin ingin menutup pengaduan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tutup',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(ComplaintUrl + 'chat/closeThread', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_thread: threadId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menutup thread'
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengaduan telah ditutup.'
                    });

                    const card = btn.closest('.pengaduan-card');

                    // tandai status di DOM
                    card.dataset.status = 'closed';

                    // ganti tombol jadi badge CLOSED
                    btn.outerHTML = `
                <span class="badge bg-secondary">
                    <i class="fas fa-lock"></i> Closed
                </span>
            `;

                    // hapus form reply + quick reply
                    card.querySelectorAll('.form-reply, .quick-reply-wrap').forEach(el => el.remove());
                });
        });
    });
</script>

<script>
    $(function() {
        // ===== AJAX CREATE PENGADUAN =====
        $('#formCreate').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: ComplaintUrl + 'chat/thread',
                method: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Pengaduan terkirim.'
                        });
                        loadPengaduan();
                        $('#modalCreatePengaduan').modal('hide');
                        $('#formCreate')[0].reset();
                        const $counter = $('#charCount');
                        if ($counter.length) $counter.text('0 / 1000');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal mengirim pengaduan.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan koneksi ke server.'
                    });
                }
            });
        });

        // ===== AJAX REPLY PENGADUAN =====
        $(document).on('submit', '.form-reply', function(e) {
            const card = this.closest('.pengaduan-card');

            if (card && card.dataset.status === 'closed') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Thread Ditutup',
                    text: 'Pengaduan ini sudah ditutup dan tidak bisa dibalas.'
                });
                return;
            }
            e.preventDefault();
            const $form = $(this);
            const id = $form.data('id');

            $.ajax({
                url: ComplaintUrl + 'chat/thread/reply/' + id,
                method: 'POST',
                dataType: 'json',
                data: $form.serialize(),
                success: function(res) {
                    if (res.status === 'success' || res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Balasan terkirim.'
                        });
                        loadPengaduan();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal mengirim balasan.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim balasan.'
                    });
                }
            });
        });

        // ===== RELOAD LIST PENGADUAN =====
        window.loadPengaduan = function() {
            $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
                const $html = $(html);
                const $newList = $html.find('#listPengaduan').html();
                $('#listPengaduan').html($newList);

                if (typeof window.applyFilter === 'function') {
                    window.applyFilter();
                }
                if (typeof window.initAutoResize === 'function') {
                    window.initAutoResize();
                }

                cleanupClosedThreads();
            });
        };

        // ===== QUICK REPLY HANDLER =====
        $(document).on('click', '.btn-quick-reply', function() {
            const text = $(this).data('text') || '';
            const mode = String($(this).data('mode') || 'replace').toLowerCase();
            const $wrap = $(this).closest('.quick-reply-wrap');
            const targetSel = $wrap.data('target');
            const $ta = targetSel ? $(targetSel) : $wrap.nextAll('form.form-reply:first').find('textarea[name="isi"]');

            if (!$ta.length) return;

            const current = $ta.val() || '';
            const next = (mode === 'append') ? (current ? (current + text) : text) : text;

            $ta.val(next).trigger('input').focus();
        });
    });

    function cleanupClosedThreads() {
        document.querySelectorAll('.pengaduan-card[data-status="closed"]').forEach(card => {
            card.querySelectorAll('.form-reply, .quick-reply-wrap').forEach(el => el.remove());
        });
    }
</script>
<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-filter-status');
        if (!btn) return;

        const status = btn.dataset.status;

        document.querySelectorAll('.pengaduan-card').forEach(card => {
            if (!status || card.dataset.status === status) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // active state
        document.querySelectorAll('.btn-filter-status').forEach(b => {
            b.classList.remove('active');
        });
        btn.classList.add('active');
    });
</script>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-change-status');
        if (!btn) return;

        // 🔒 guard role (data-role bisa kamu inject kalau mau)
        const allowedRoles = ['sudo', 'monitoring'];
        const currentRole = '<?= session()->get('role') ?>';

        if (!allowedRoles.includes(currentRole)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: 'Anda tidak memiliki izin untuk mengubah status pengaduan.'
            });
            return;
        }

        e.preventDefault();

        const threadId = btn.dataset.id;
        const newStatus = btn.dataset.status;

        const statusText = {
            open: 'Open',
            in_progress: 'In Progress',
            resolved: 'Resolved',
            closed: 'Closed'
        };

        Swal.fire({
            title: 'Ubah Status?',
            text: `Ubah status pengaduan menjadi "${statusText[newStatus]}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(ComplaintUrl + 'chat/updateStatusThread', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_thread: threadId,
                        role: currentRole,
                        status: newStatus
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        Swal.fire('Gagal', res.message || 'Gagal update status', 'error');
                        return;
                    }

                    Swal.fire('Berhasil', 'Status berhasil diubah', 'success');

                    // reload agar konsisten server-side
                    loadPengaduan();
                })
                .catch(() => {
                    Swal.fire('Error', 'Koneksi ke server gagal', 'error');
                });
        });
    });
</script>


<?php if (isset($enable_polling) && $enable_polling): ?>
    <script>
        // Polling untuk update real-time (opsional)
        let pollingInterval = null;

        function startPolling() {
            if (pollingInterval) clearInterval(pollingInterval);

            pollingInterval = setInterval(() => {
                $.get('<?= base_url($role . "/pengaduan/checkUpdates") ?>', {
                    last_update: new Date().getTime()
                }, function(data) {
                    if (data.has_updates) {
                        loadPengaduan();
                    }
                });
            }, 10000); // Poll setiap 10 detik
        }

        // Mulai polling
        startPolling();
    </script>
<?php endif; ?>

<?= $this->endSection() ?>