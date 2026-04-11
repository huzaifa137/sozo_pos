@extends('layouts.app')
@section('page-title', 'Stock Batches')

@push('styles')
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 1.5rem;
            align-items: start
        }

        .settings-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.6rem;
            position: sticky;
            top: 76px
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
            gap: .5rem
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
            letter-spacing: .05em
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
            appearance: none
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(240, 192, 64, .1)
        }

        .error-msg {
            color: #fca5a5;
            font-size: .76rem;
            margin-top: .3rem
        }

        .code-hint {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .3rem
        }

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
            transition: background .2s, border-color .2s
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
            transition: all .2s
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
            transition: background .18s
        }

        .btn-submit:hover {
            background: #ffd55e
        }

        /* batch number preview */
        .batch-preview {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: .5rem .8rem;
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: .6rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .batch-preview strong {
            color: var(--accent);
            font-family: var(--font-head);
            font-weight: 700
        }

        .batch-preview svg {
            flex-shrink: 0
        }

        /* table */
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
            justify-content: space-between
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
            background: var(--surface2)
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
            color: var(--muted)
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: .4rem
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
            text-decoration: none
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

        /* modal */
        .modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .65);
            z-index: 500;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px)
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
            overflow-y: auto
        }

        .modal-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.3rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid var(--border)
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
            transition: color .13s
        }

        .modal-close:hover {
            color: var(--text)
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
    <a href="{{ route('subcategories.index') }}" class="topbar-btn tb-outline">Sub Categories</a>

@endsection

@section('content')

    <div class="settings-grid">

        {{-- ── LEFT: ADD FORM ── --}}
        <div class="settings-card">
            <h3>
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Add Stock Batch
            </h3>

            <div class="batch-preview" id="batchPreview">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" />
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                    <circle cx="5.5" cy="18.5" r="2.5" />
                    <circle cx="18.5" cy="18.5" r="2.5" />
                </svg>
                Preview: <strong id="batchPreviewVal">STK-YYYY-NNN</strong>
            </div>

            <form action="{{ route('stock-batches.store') }}" method="POST" id="addBatchForm">
                @csrf

                <div class="form-group">
                    <label>Batch Number *</label>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="e.g. STK-2026-002"
                        required oninput="updatePreview(this.value); suggestBatchCode(this.value)">
                    <p class="code-hint">Format: STK-YYYY-NNN — must be unique.</p>
                    @error('batch_number')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Display Name *</label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}"
                        placeholder="e.g. STK-2026-002 — May Batch" required id="displayNameField">
                    @error('display_name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Code *
                        <span style="font-size:.7rem;font-weight:400;text-transform:none;color:var(--muted)">— unique DB
                            identifier</span>
                    </label>
                    <input type="text" name="code" id="batchCodeField" value="{{ old('code') }}"
                        placeholder="e.g. STK-2026-002" required>
                    <p class="code-hint">Auto-filled from batch number. Upper-cased on save.</p>
                    @error('code')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Description <span style="font-weight:400;text-transform:none">(optional)</span></label>
                    <textarea name="description" rows="2"
                        placeholder="e.g. May 2026 purchase batch">{{ old('description') }}</textarea>
                </div>

                <div class="toggle-row">
                    <div>
                        <div class="toggle-label-txt">Active</div>
                        <div class="toggle-sub">Inactive batches won't appear in dropdowns</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="addBatchBtn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                        </svg>
                        <span class="btn-text">Save Batch</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ── RIGHT: TABLE ── --}}
        <div class="table-card">
            <div class="table-card-hdr">
                <h3>All Stock Batches</h3>
                <span class="table-count">{{ $batches->count() }} total</span>
            </div>

            @if($batches->isEmpty())
                <div style="padding:3rem;text-align:center;color:var(--muted);font-size:.88rem">
                    No stock batches yet. Add your first one.
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Code</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-family:var(--font-head)">{{ $batch->batch_number }}</div>
                                    <div style="font-size:.75rem;color:var(--muted)">{{ $batch->display_name }}</div>
                                </td>
                                <td><span class="code-chip">{{ $batch->code }}</span></td>
                                <td>
                                    <span style="font-family:var(--font-head);font-weight:700;font-size:.9rem;color:var(--accent)">
                                        {{ $batch->inventory_items_count }}
                                    </span>
                                </td>
                                <td>
                                    <span style="display:inline-flex;align-items:center;font-size:.78rem">
                                        <span
                                            class="status-dot {{ $batch->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                        {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="color:var(--muted);font-size:.78rem">{{ $batch->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display:flex;gap:.4rem">
                                        <button class="action-btn ab-edit" onclick="openEditModal(
                                                    {{ $batch->id }},
                                                    '{{ addslashes($batch->batch_number) }}',
                                                    '{{ addslashes($batch->display_name) }}',
                                                    '{{ $batch->code }}',
                                                    '{{ addslashes($batch->description ?? '') }}',
                                                    {{ $batch->is_active ? 'true' : 'false' }}
                                                )">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form class="del-form" action="{{ route('stock-batches.destroy', $batch) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn ab-del" data-name="{{ $batch->batch_number }}"
                                                data-count="{{ $batch->inventory_items_count }}">
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
                <h3>Edit Stock Batch</h3>
                <button class="modal-close" onclick="closeEditModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            <form id="editBatchForm" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Batch Number *</label>
                    <input type="text" name="batch_number" id="edit_batch_number" required>
                    @error('batch_number')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Display Name *</label>
                    <input type="text" name="display_name" id="edit_display_name" required>
                </div>

                <div class="form-group">
                    <label>Code *</label>
                    <input type="text" name="code" id="edit_code" required>
                    <p class="code-hint">Changing code will break links if items already use it.</p>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" rows="2"></textarea>
                </div>

                <div class="toggle-row">
                    <div>
                        <div class="toggle-label-txt">Active</div>
                        <div class="toggle-sub">Inactive batches won't appear in dropdowns</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-submit" id="editBatchBtn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        </svg>
                        <span class="btn-text">Update Batch</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ── BATCH NUMBER helpers ── */
        function updatePreview(val) {
            document.getElementById('batchPreviewVal').textContent = val || 'STK-YYYY-NNN';
        }
        function suggestBatchCode(val) {
            const cf = document.getElementById('batchCodeField');
            if (!cf.dataset.manual) cf.value = val.toUpperCase().replace(/[^A-Z0-9-]+/g, '');
            // Auto-fill display name hint
            const dn = document.getElementById('displayNameField');
            if (!dn.dataset.manual && val) dn.value = val + ' — Batch';
        }
        document.getElementById('batchCodeField')?.addEventListener('input', function () { this.dataset.manual = '1'; });
        document.getElementById('displayNameField')?.addEventListener('input', function () { this.dataset.manual = '1'; });

        /* ── ADD FORM confirm ── */
        document.getElementById('addBatchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this, btn = document.getElementById('addBatchBtn');
            Swal.fire({
                title: 'Save Batch?', text: 'Add this stock batch to the system?', icon: 'question',
                background: 'var(--surface)', color: 'var(--text)',
                showCancelButton: true, confirmButtonColor: '#f0c040', cancelButtonColor: '#374151',
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
        function openEditModal(id, batchNum, displayName, code, description, isActive) {
            document.getElementById('editBatchForm').action = '/stock-batches/' + id;
            document.getElementById('edit_batch_number').value = batchNum;
            document.getElementById('edit_display_name').value = displayName;
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_description').value = description;
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

        /* ── EDIT FORM confirm ── */
        document.getElementById('editBatchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this, btn = document.getElementById('editBatchBtn');
            Swal.fire({
                title: 'Update Batch?', icon: 'question',
                background: 'var(--surface)', color: 'var(--text)',
                showCancelButton: true, confirmButtonColor: '#f0c040', cancelButtonColor: '#374151',
                confirmButtonText: 'Yes, update!',
            }).then(r => {
                if (r.isConfirmed) {
                    btn.disabled = true;
                    btn.querySelector('.btn-text').textContent = 'Updating…';
                    form.submit();
                }
            });
        });

        /* ── DELETE confirm ── */
        document.querySelectorAll('.del-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = this.querySelector('button[type=submit]');
                const name = btn.dataset.name;
                const count = parseInt(btn.dataset.count);
                if (count > 0) {
                    Swal.fire({
                        title: 'Cannot Delete',
                        text: `"${name}" has ${count} linked item${count > 1 ? 's' : ''}. Remove them first.`,
                        icon: 'error', background: 'var(--surface)', color: 'var(--text)', confirmButtonColor: '#f0c040',
                    }); return;
                }
                Swal.fire({
                    title: `Delete "${name}"?`, text: 'This cannot be undone.', icon: 'warning',
                    background: 'var(--surface)', color: 'var(--text)',
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#374151',
                    confirmButtonText: 'Yes, delete!',
                }).then(r => { if (r.isConfirmed) form.submit(); });
            });
        });

        /* ── Flash toasts ── */
        @if(session('success'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: "{{ addslashes(session('success')) }}",
                showConfirmButton: false, timer: 2800, timerProgressBar: true,
                background: 'var(--surface)', color: 'var(--text)', iconColor: '#f0c040'
            });
        @endif
        @if(session('error'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'error',
                title: "{{ addslashes(session('error')) }}",
                showConfirmButton: false, timer: 3500,
                background: 'var(--surface)', color: 'var(--text)'
            });
        @endif
    </script>
@endpush