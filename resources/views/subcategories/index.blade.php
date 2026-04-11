@extends('layouts.app')
@section('page-title', 'Subcategories')

@push('styles')
    <style>
        /* ── PAGE LAYOUT ── */
        .settings-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 1.5rem;
            align-items: start
        }

        /* ── FORM CARD (shared style matching inventory create) ── */
        .settings-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.6rem;
            position: sticky;
            top: 76px;
        }

        .settings-card h3 {
            font-family: var(--font-head);
            font-weight: 700;
            font-size: .95rem;
            margin-bottom: 1.3rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .form-group {
            margin-bottom: 1rem
        }

        label {
            display: block;
            font-size: .76rem;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: .4rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        input[type=text],
        input[type=number],
        textarea,
        select {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            padding: .65rem .9rem;
            font-family: var(--font-body);
            font-size: .9rem;
            outline: none;
            transition: border-color .18s, box-shadow .18s;
            appearance: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(240, 192, 64, .1);
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .85rem center;
            padding-right: 2.3rem;
        }

        .error-msg {
            color: #fca5a5;
            font-size: .76rem;
            margin-top: .3rem
        }

        /* toggle switch */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .6rem 0
        }

        .toggle-label-txt {
            font-size: .88rem;
            font-weight: 500;
            color: var(--text)
        }

        .toggle-sub {
            font-size: .75rem;
            color: var(--muted);
            margin-top: .1rem
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 24px;
            cursor: pointer;
            transition: background .2s, border-color .2s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background: var(--muted);
            border-radius: 50%;
            transition: all .2s;
        }

        .toggle-switch input:checked+.toggle-slider {
            background: rgba(34, 197, 94, .2);
            border-color: rgba(34, 197, 94, .4)
        }

        .toggle-switch input:checked+.toggle-slider::before {
            left: 23px;
            background: var(--success)
        }

        .form-actions {
            display: flex;
            gap: .8rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            margin-top: 1.2rem
        }

        .btn-submit {
            flex: 1;
            background: var(--accent);
            color: #0b0d11;
            border: none;
            border-radius: 8px;
            padding: .8rem 1.4rem;
            font-family: var(--font-head);
            font-size: .95rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: background .18s;
        }

        .btn-submit:hover {
            background: #ffd55e
        }

        /* code hint */
        .code-hint {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .3rem
        }

        /* ── TABLE ── */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden
        }

        .table-card-hdr {
            padding: .85rem 1.2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-hdr h3 {
            font-family: var(--font-head);
            font-weight: 700;
            font-size: .95rem
        }

        .table-count {
            font-size: .78rem;
            color: var(--muted);
            background: var(--surface2);
            border-radius: 20px;
            padding: .15rem .65rem;
            font-weight: 600
        }

        .data-table {
            width: 100%;
            border-collapse: collapse
        }

        .data-table th {
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            padding: .7rem 1.1rem;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
        }

        .data-table td {
            padding: .8rem 1.1rem;
            font-size: .88rem;
            border-bottom: 1px solid rgba(255, 255, 255, .04)
        }

        .data-table tr:last-child td {
            border-bottom: none
        }

        .data-table tr:hover td {
            background: rgba(255, 255, 255, .02)
        }

        .code-chip {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: .18rem .5rem;
            font-size: .75rem;
            font-weight: 600;
            font-family: monospace;
            color: var(--muted);
        }

        .category-badge {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: .18rem .5rem;
            font-size: .75rem;
            font-weight: 500;
            color: var(--accent2);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: .4rem;
            flex-shrink: 0
        }

        .status-active {
            background: var(--success)
        }

        .status-inactive {
            background: var(--muted)
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .32rem .7rem;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all .13s;
            border: none;
            text-decoration: none;
        }

        .ab-edit {
            background: var(--surface2);
            color: var(--text);
            border: 1px solid var(--border)
        }

        .ab-edit:hover {
            border-color: var(--accent2);
            color: var(--accent2)
        }

        .ab-del {
            background: rgba(239, 68, 68, .08);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, .2)
        }

        .ab-del:hover {
            background: rgba(239, 68, 68, .18)
        }

        /* ── EDIT MODAL ── */
        .modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .65);
            z-index: 500;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }

        .modal-bg.open {
            display: flex
        }

        .modal-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: 460px;
            max-width: 96vw;
            padding: 1.6rem;
            max-height: 92vh;
            overflow-y: auto;
        }

        .modal-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.3rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid var(--border);
        }

        .modal-hdr h3 {
            font-family: var(--font-head);
            font-weight: 700;
            font-size: 1rem
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 5px;
            transition: color .13s;
        }

        .modal-close:hover {
            color: var(--text)
        }

        .btn-outline {
            flex: 1;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .8rem 1.4rem;
            font-family: var(--font-head);
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s;
        }

        .btn-outline:hover {
            background: var(--surface2);
            border-color: var(--muted)
        }

        @media(max-width:1000px) {
            .settings-grid {
                grid-template-columns: 1fr
            }

            .settings-card {
                position: static
            }
        }
    </style>
@endpush

@section('topbar-actions')
    <a href="{{ route('categories.index') }}" class="topbar-btn tb-outline">Categories</a>
    <a href="{{ route('stock-batches.index') }}" class="topbar-btn tb-outline">Stock Batches</a>
@endsection

@section('content')

    <div class="settings-grid">

        {{-- ── LEFT: ADD FORM ── --}}
        <div class="settings-card">
            <h3>
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Add Subcategory
            </h3>

            <form action="{{ route('subcategories.store') }}" method="POST" id="addSubcatForm">
                @csrf

                <div class="form-group">
                    <label>Parent Category *</label>
                    <select name="category_code" required>
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->code }}" {{ old('category_code') == $cat->code ? 'selected' : '' }}>
                                {{ $cat->display_name }} ({{ $cat->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_code')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Internal Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. smartphones" required
                        oninput="suggestCode(this.value)">
                    <p class="code-hint">Short internal identifier — no spaces.</p>
                    @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Display Name *</label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}"
                        placeholder="e.g. Smartphones & Tablets" required>
                    @error('display_name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Subcategory Code *
                        <span style="font-size:.7rem;font-weight:400;text-transform:none;color:var(--muted)">— used in DB
                            relations</span>
                    </label>
                    <input type="text" name="code" id="codeField" value="{{ old('code') }}" placeholder="e.g. smartphones"
                        required>
                    <p class="code-hint">Auto-slug generated. Must be unique. Cannot be changed later if items are linked.
                    </p>
                    @error('code')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="toggle-row">
                    <div>
                        <div class="toggle-label-txt">Active</div>
                        <div class="toggle-sub">Inactive subcategories won't appear in dropdowns</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="addSubcatBtn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                        </svg>
                        <span class="btn-text">Save Subcategory</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ── RIGHT: TABLE ── --}}
        <div class="table-card">
            <div class="table-card-hdr">
                <h3>All Subcategories</h3>
                <span class="table-count">{{ $subcategories->count() }} total</span>
            </div>

            @if($subcategories->isEmpty())
                <div style="padding:3rem;text-align:center;color:var(--muted);font-size:.88rem">
                    No subcategories yet. Add your first one.
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Display Name</th>
                            <th>Code</th>
                            <th>Parent Category</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subcategories as $subcat)
                            <tr>
                                <td>
                                    <div style="font-weight:600">{{ $subcat->display_name }}</div>
                                    <div style="font-size:.75rem;color:var(--muted)">{{ $subcat->name }}</div>
                                </td>
                                <td><span class="code-chip">{{ $subcat->code }}</span></td>
                                <td>
                                    @if($subcat->category)
                                        <span class="category-badge">{{ $subcat->category->display_name }}</span>
                                    @else
                                        <span style="color:var(--muted);font-size:.75rem">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-family:var(--font-head);font-weight:700;font-size:.9rem;color:var(--accent)">
                                        {{ $subcat->inventory_items_count }}
                                    </span>
                                </td>
                                <td>
                                    <span style="display:inline-flex;align-items:center;font-size:.78rem">
                                        <span
                                            class="status-dot {{ $subcat->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                        {{ $subcat->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="color:var(--muted);font-size:.78rem">{{ $subcat->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display:flex;gap:.4rem">
                                        <button class="action-btn ab-edit" onclick="openEditModal(
                                                    {{ $subcat->id }},
                                                    '{{ addslashes($subcat->category_code) }}',
                                                    '{{ addslashes($subcat->name) }}',
                                                    '{{ addslashes($subcat->display_name) }}',
                                                    '{{ $subcat->code }}',
                                                    {{ $subcat->is_active ? 'true' : 'false' }}
                                                )">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form class="del-form" action="{{ route('subcategories.destroy', $subcat) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn ab-del" data-name="{{ $subcat->display_name }}"
                                                data-count="{{ $subcat->inventory_items_count }}">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ── EDIT MODAL ── --}}
    <div class="modal-bg" id="editModal">
        <div class="modal-box">
            <div class="modal-hdr">
                <h3>Edit Subcategory</h3>
                <button class="modal-close" onclick="closeEditModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            <form id="editSubcatForm" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Parent Category *</label>
                    <select name="category_code" id="edit_category_code" required>
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->code }}">{{ $cat->display_name }} ({{ $cat->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Internal Name *</label>
                    <input type="text" name="name" id="edit_name" required>
                    @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Display Name *</label>
                    <input type="text" name="display_name" id="edit_display_name" required>
                </div>

                <div class="form-group">
                    <label>Subcategory Code *</label>
                    <input type="text" name="code" id="edit_code" required>
                    <p class="code-hint">Changing code will break links if items already use it.</p>
                </div>

                <div class="toggle-row">
                    <div>
                        <div class="toggle-label-txt">Active</div>
                        <div class="toggle-sub">Inactive subcategories won't appear in dropdowns</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-outline" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-submit" id="editSubcatBtn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        </svg>
                        <span class="btn-text">Update Subcategory</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ── CODE SLUG SUGGESTION ── */
        function suggestCode(val) {
            const code = document.getElementById('codeField');
            if (!code.dataset.manual) {
                code.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }
        }
        document.getElementById('codeField')?.addEventListener('input', function () {
            this.dataset.manual = '1';
        });

        /* ── ADD FORM — SweetAlert confirm ── */
        document.getElementById('addSubcatForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = document.getElementById('addSubcatBtn');
            Swal.fire({
                title: 'Save Subcategory?',
                text: 'Add this subcategory to your system?',
                icon: 'question',
                background: 'var(--surface)',
                color: 'var(--text)',
                showCancelButton: true,
                confirmButtonColor: '#f0c040',
                cancelButtonColor: '#374151',
                confirmButtonText: 'Yes, save it!',
            }).then(r => {
                if (r.isConfirmed) {
                    btn.disabled = true;
                    btn.querySelector('.btn-text').textContent = 'Saving…';
                    form.submit();
                }
            });
        });

        /* ── EDIT MODAL ── */
        function openEditModal(id, categoryCode, name, displayName, code, isActive) {
            document.getElementById('editSubcatForm').action = '/subcategories/' + id;
            document.getElementById('edit_category_code').value = categoryCode;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_display_name').value = displayName;
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_is_active').checked = isActive;
            document.getElementById('editModal').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) closeEditModal();
        });

        /* ── EDIT FORM — SweetAlert confirm ── */
        document.getElementById('editSubcatForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const btn = document.getElementById('editSubcatBtn');
            Swal.fire({
                title: 'Update Subcategory?',
                text: 'Save changes to this subcategory?',
                icon: 'question',
                background: 'var(--surface)',
                color: 'var(--text)',
                showCancelButton: true,
                confirmButtonColor: '#f0c040',
                cancelButtonColor: '#374151',
                confirmButtonText: 'Yes, update!',
            }).then(r => {
                if (r.isConfirmed) {
                    btn.disabled = true;
                    btn.querySelector('.btn-text').textContent = 'Updating…';
                    form.submit();
                }
            });
        });

        /* ── DELETE — SweetAlert confirm ── */
        document.querySelectorAll('.del-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = this.querySelector('button[type=submit]');
                const name = btn.dataset.name;
                const count = parseInt(btn.dataset.count);

                if (count > 0) {
                    Swal.fire({
                        title: 'Cannot Delete',
                        text: `"${name}" has ${count} linked item${count > 1 ? 's' : ''}. Remove or re-categorise them first.`,
                        icon: 'error',
                        background: 'var(--surface)',
                        color: 'var(--text)',
                        confirmButtonColor: '#f0c040',
                    });
                    return;
                }

                Swal.fire({
                    title: `Delete "${name}"?`,
                    text: 'This cannot be undone.',
                    icon: 'warning',
                    background: 'var(--surface)',
                    color: 'var(--text)',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#374151',
                    confirmButtonText: 'Yes, delete!',
                }).then(r => {
                    if (r.isConfirmed) form.submit();
                });
            });
        });

        /* ── Flash success toast ── */
        @if(session('success'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: "{{ addslashes(session('success')) }}",
                showConfirmButton: false, timer: 2800, timerProgressBar: true,
                background: 'var(--surface)', color: 'var(--text)', iconColor: '#f0c040',
            });
        @endif
        @if(session('error'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: "{{ addslashes(session('error')) }}",
                showConfirmButton: false, timer: 3500,
                background: 'var(--surface)', color: 'var(--text)',
            });
        @endif
    </script>
@endpush