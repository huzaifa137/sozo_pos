@extends('layouts.app')
@section('page-title', 'Add Inventory Item')

@push('styles')
    <style>
        /* ─── LAYOUT ─────────────────────────────────────── */
        .form-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 1.8rem;
            align-items: start
        }

        /* ─── IMAGE PICKER CARD ──────────────────────────── */
        .img-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            position: sticky;
            top: 76px
        }

        .img-card-hdr {
            padding: .85rem 1.1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .img-card-hdr h3 {
            font-family: var(--font-head);
            font-weight: 700;
            font-size: .95rem
        }

        .img-badge {
            font-size: .7rem;
            font-weight: 700;
            padding: .18rem .55rem;
            border-radius: 20px;
            border: 1px solid;
            background: rgba(240, 192, 64, .1);
            color: var(--accent);
            border-color: rgba(240, 192, 64, .25)
        }

        .img-badge.has-img {
            background: rgba(34, 197, 94, .1);
            color: #86efac;
            border-color: rgba(34, 197, 94, .3)
        }

        /* Preview area */
        .img-preview-area {
            position: relative;
            width: 100%;
            aspect-ratio: 4/3;
            background: #0b0d11;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .img-preview-area img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none
        }

        .img-preview-area img.visible {
            display: block
        }

        .img-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .6rem;
            color: var(--muted)
        }

        .img-placeholder svg {
            opacity: .35
        }

        .img-placeholder p {
            font-size: .8rem
        }

        /* Picker buttons */
        .img-picker-btns {
            display: flex;
            gap: .6rem;
            padding: .9rem;
            border-bottom: 1px solid var(--border)
        }

        .pick-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .35rem;
            padding: .75rem .5rem;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--surface2);
            cursor: pointer;
            color: var(--muted);
            font-family: var(--font-body);
            font-size: .78rem;
            font-weight: 600;
            transition: all .18s
        }

        .pick-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(240, 192, 64, .06)
        }

        .pick-btn svg {
            flex-shrink: 0
        }

        /* Status bar */
        .img-status {
            padding: .55rem 1rem;
            font-size: .75rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: .45rem;
            min-height: 32px
        }

        .img-status .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            flex-shrink: 0
        }

        @keyframes dotPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .4;
                transform: scale(.7)
            }
        }

        .img-status .dot {
            animation: dotPulse 1.5s infinite
        }

        .img-status .remove-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: .72rem;
            display: flex;
            align-items: center;
            gap: .25rem;
            transition: color .15s;
            font-family: var(--font-body)
        }

        .img-status .remove-btn:hover {
            color: var(--danger)
        }

        /* ─── FULL-SCREEN CAMERA MODAL ───────────────────── */
        .cam-modal {
            position: fixed;
            inset: 0;
            z-index: 9000;
            background: #000;
            display: none;
            flex-direction: column;
            font-family: var(--font-body);
        }

        .cam-modal.open {
            display: flex
        }

        /* top bar */
        .cam-topbar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: env(safe-area-inset-top, .6rem) 1rem .6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(to bottom, rgba(0, 0, 0, .7), transparent);
            z-index: 20;
        }

        .cam-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(6px);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .cam-icon-btn:hover {
            background: rgba(255, 255, 255, .28)
        }

        .cam-topbar-title {
            color: #fff;
            font-weight: 600;
            font-size: .95rem;
            letter-spacing: .02em
        }

        /* viewfinder */
        .cam-viewfinder {
            flex: 1;
            position: relative;
            overflow: hidden
        }

        #camLive {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        #camCanvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none
        }

        /* guide corners */
        .cam-guide {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 5
        }

        .cam-guide-box {
            width: min(72vw, 72vh);
            aspect-ratio: 1;
            position: relative
        }

        .cam-guide-box::before,
        .cam-guide-box::after,
        .cam-guide-box span::before,
        .cam-guide-box span::after {
            content: '';
            position: absolute;
            width: 26px;
            height: 26px;
            border-color: rgba(240, 192, 64, .9);
            border-style: solid;
        }

        .cam-guide-box::before {
            top: 0;
            left: 0;
            border-width: 3px 0 0 3px
        }

        .cam-guide-box::after {
            top: 0;
            right: 0;
            border-width: 3px 3px 0 0
        }

        .cam-guide-box span::before {
            bottom: 0;
            left: 0;
            border-width: 0 0 3px 3px
        }

        .cam-guide-box span::after {
            bottom: 0;
            right: 0;
            border-width: 0 3px 3px 0
        }

        /* grid overlay */
        .cam-grid-overlay {
            position: absolute;
            inset: 0;
            z-index: 4;
            pointer-events: none;
            display: none
        }

        .cam-grid-overlay.show {
            display: block
        }

        .cam-grid-overlay::before,
        .cam-grid-overlay::after {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, .18)
        }

        .cam-grid-overlay::before {
            top: 0;
            bottom: 0;
            left: 33.33%;
            right: 33.33%;
            border-left: 1px solid;
            border-right: 1px solid;
            border-color: rgba(255, 255, 255, .18)
        }

        .cam-grid-overlay::after {
            left: 0;
            right: 0;
            top: 33.33%;
            bottom: 33.33%;
            border-top: 1px solid;
            border-bottom: 1px solid;
            border-color: rgba(255, 255, 255, .18)
        }

        /* scan line */
        @keyframes scanMove {
            0% {
                top: 12%
            }

            100% {
                top: 88%
            }
        }

        .cam-scan {
            position: absolute;
            left: 0;
            right: 0;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, rgba(240, 192, 64, .85), transparent);
            z-index: 6;
            pointer-events: none;
            animation: scanMove 2.8s ease-in-out infinite alternate
        }

        /* shutter flash */
        @keyframes shutterAnim {

            0%,
            100% {
                opacity: 0
            }

            20% {
                opacity: .85
            }
        }

        .cam-flash {
            position: absolute;
            inset: 0;
            background: #fff;
            opacity: 0;
            pointer-events: none;
            z-index: 25
        }

        .cam-flash.firing {
            animation: shutterAnim .3s ease
        }

        /* loading */
        .cam-loading {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .88);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .9rem;
            z-index: 30;
            color: #fff;
            font-size: .9rem
        }

        .cam-loading.hidden {
            display: none
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .cam-spinner {
            width: 38px;
            height: 38px;
            border: 3px solid rgba(255, 255, 255, .18);
            border-top-color: #f0c040;
            border-radius: 50%;
            animation: spin .75s linear infinite
        }

        /* preview overlay */
        .cam-preview {
            position: absolute;
            inset: 0;
            z-index: 22;
            display: none;
            flex-direction: column
        }

        .cam-preview.show {
            display: flex
        }

        .cam-preview img {
            flex: 1;
            width: 100%;
            object-fit: cover
        }

        .cam-preview-bar {
            display: flex;
            gap: .7rem;
            padding: .9rem 1.2rem;
            background: rgba(0, 0, 0, .82);
            backdrop-filter: blur(10px)
        }

        .cam-preview-bar button {
            flex: 1;
            padding: .72rem;
            border-radius: 10px;
            border: none;
            font-family: var(--font-body);
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            transition: all .15s
        }

        .cam-preview-retake {
            background: rgba(255, 255, 255, .14);
            color: #fff
        }

        .cam-preview-retake:hover {
            background: rgba(255, 255, 255, .25)
        }

        .cam-preview-use {
            background: #f0c040;
            color: #0b0d11
        }

        .cam-preview-use:hover {
            background: #ffd55e
        }

        /* bottom controls */
        .cam-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: .7rem 1.4rem calc(env(safe-area-inset-bottom, .8rem) + .8rem);
            background: linear-gradient(to top, rgba(0, 0, 0, .82), transparent);
            backdrop-filter: blur(8px);
            z-index: 20;
        }

        .cam-zoom-row {
            display: flex;
            align-items: center;
            gap: .8rem;
            margin-bottom: .8rem
        }

        .cam-zoom-minus,
        .cam-zoom-plus {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, .16);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .14s
        }

        .cam-zoom-minus:hover,
        .cam-zoom-plus:hover {
            background: rgba(255, 255, 255, .3)
        }

        .cam-zoom-slider {
            flex: 1;
            -webkit-appearance: none;
            height: 3px;
            background: rgba(255, 255, 255, .25);
            border-radius: 2px;
            outline: none;
            cursor: pointer
        }

        .cam-zoom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f0c040;
            cursor: pointer;
            box-shadow: 0 0 4px rgba(0, 0, 0, .5)
        }

        .cam-zoom-label {
            color: #f0c040;
            font-size: .8rem;
            font-weight: 700;
            min-width: 34px;
            text-align: right;
            font-family: monospace
        }

        .cam-shutter-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 .4rem
        }

        .cam-side-btn {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .85);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .14s;
            flex-shrink: 0
        }

        .cam-side-btn:hover {
            background: rgba(255, 255, 255, .24)
        }

        .cam-side-btn.active {
            color: #f0c040;
            background: rgba(240, 192, 64, .18)
        }

        .cam-shutter {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform .1s;
            flex-shrink: 0
        }

        .cam-shutter:active {
            transform: scale(.9)
        }

        .cam-shutter-ring {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, .85);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color .14s
        }

        .cam-shutter:hover .cam-shutter-ring {
            border-color: #f0c040
        }

        .cam-shutter-disc {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fff;
            transition: background .14s
        }

        .cam-shutter:hover .cam-shutter-disc {
            background: #f0c040
        }

        /* hidden file input */
        .hidden-file {
            display: none
        }

        /* ─── FIELDS CARD ────────────────────────────────── */
        .fields-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.6rem
        }

        .fields-card h3 {
            font-family: var(--font-head);
            font-weight: 700;
            font-size: .95rem;
            margin-bottom: 1.2rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid var(--border)
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .9rem
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

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .85rem center;
            padding-right: 2.3rem
        }

        .input-pfx {
            position: relative
        }

        .input-pfx span {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .85rem;
            pointer-events: none
        }

        .input-pfx input {
            padding-left: 2.8rem
        }

        .error-msg {
            color: #fca5a5;
            font-size: .76rem;
            margin-top: .3rem
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

        @media(max-width:900px) {
            .form-grid {
                grid-template-columns: 1fr
            }

            .img-card {
                position: static
            }
        }

        @media(max-width:600px) {
            .form-row {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush

@section('content')
    <div style="margin-bottom:1.5rem">
        <h1 style="font-family:var(--font-head);font-size:1.8rem;font-weight:800;letter-spacing:-.5px">Add Inventory Item
        </h1>
        <p style="color:var(--muted);margin-top:.3rem;font-size:.9rem">Capture or upload a photo, then fill in the product
            details.</p>
    </div>

    {{-- Hidden inputs --}}
    <input type="hidden" id="imageData" name="image_data_global">

    <form action="{{ route('inventory.store') }}" method="POST" id="inventoryForm">
        @csrf
        <input type="hidden" name="image_data" id="imageDataField">

        <div class="form-grid">

            {{-- ─── IMAGE PICKER ─── --}}
            <div class="img-card">
                <div class="img-card-hdr">
                    <h3>Item Photo</h3>
                    <span class="img-badge" id="imgBadge">No photo</span>
                </div>

                {{-- Preview --}}
                <div class="img-preview-area" id="imgPreviewArea">
                    <img id="imgPreviewEl" src="" alt="Preview">
                    <div class="img-placeholder" id="imgPlaceholder">
                        <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.3"
                            viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        <p>No photo yet</p>
                    </div>
                </div>

                {{-- Picker buttons --}}
                <div class="img-picker-btns">
                    <button type="button" class="pick-btn" onclick="openCamModal()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                            <circle cx="12" cy="13" r="4" />
                        </svg>
                        Take Photo
                    </button>
                    <label class="pick-btn" style="cursor:pointer">
                        <input type="file" accept="image/*" class="hidden-file" id="fileInput" onchange="handleFile(event)">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        Upload File
                    </label>
                </div>

                {{-- Status bar --}}
                <div class="img-status" id="imgStatus">
                    <span style="color:var(--muted);font-size:.75rem">Supports JPG, PNG, WEBP</span>
                </div>
            </div>

            {{-- ─── FORM FIELDS ─── --}}
            <div class="fields-card">
                <h3>Product Details</h3>

                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. Samsung 65W Charger">
                    @error('name')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Selling Price *</label>
                        <div class="input-pfx"><span>UGX</span>
                            <input type="number" name="selling_price" step="0.01" min="0" placeholder="0.00"
                                value="{{ old('selling_price') }}" required>
                        </div>
                        @error('selling_price')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label>Buying Price *</label>
                        <div class="input-pfx"><span>UGX</span>
                            <input type="number" name="buying_price" step="0.01" min="0" placeholder="0.00"
                                value="{{ old('buying_price') }}" required>
                        </div>
                        @error('buying_price')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select category…</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label>Subcategory *</label>
                        <select name="subcategory" id="subcategorySelect" required>
                            <option value="">Select subcategory…</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label>Stock Number *</label>
                        <select name="stock_number" required>
                            <option value="" disabled {{ old('stock_number') ? '' : 'selected' }}>Select batch…</option>
                            @foreach($stocks as $key => $label)
                                <option value="{{ $key }}" {{ old('stock_number') == $key ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_number')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label>Quantity in Stock *</label>
                        <input type="number" name="quantity" min="0" placeholder="0" value="{{ old('quantity', 1) }}"
                            required>
                        @error('quantity')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>

                <input type="number" name="low_stock_threshold" value="5" style="display:none" required>

                <div class="form-group">
                    <label>Description <span style="font-weight:400;text-transform:none">(optional)</span></label>
                    <textarea name="description" rows="3"
                        placeholder="Brief description of the item…">{{ old('description') }}</textarea>
                </div>

                <div class="form-actions">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                        </svg>
                        <span class="btn-text">Save to Inventory</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ═══════════════════════════════════════════════
    FULL-SCREEN CAMERA MODAL
    ═══════════════════════════════════════════════ --}}
    <div class="cam-modal" id="camModal" role="dialog" aria-modal="true">

        {{-- Top bar --}}
        <div class="cam-topbar">
            <button class="cam-icon-btn" onclick="closeCamModal()" title="Close">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
            <span class="cam-topbar-title" id="camTitleLabel">Back Camera</span>
            <button class="cam-icon-btn" id="flipBtn" onclick="flipCamera()" title="Flip camera">
                <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 4v6h6" />
                    <path d="M23 20v-6h-6" />
                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 0 1 3.51 15" />
                </svg>
            </button>
        </div>

        {{-- Viewfinder --}}
        <div class="cam-viewfinder" id="camViewfinder">
            <video id="camLive" autoplay playsinline muted></video>
            <canvas id="camCanvas"></canvas>

            {{-- Overlays --}}
            <div class="cam-grid-overlay" id="camGridOverlay"></div>
            <div class="cam-scan" id="camScan"></div>
            <div class="cam-guide">
                <div class="cam-guide-box"><span></span></div>
            </div>
            <div class="cam-flash" id="camFlash"></div>

            {{-- Loading --}}
            <div class="cam-loading" id="camLoading">
                <div class="cam-spinner"></div>
                <p>Starting camera…</p>
            </div>

            {{-- Preview after snap --}}
            <div class="cam-preview" id="camPreview">
                <img id="camPreviewImg" src="" alt="Captured">
                <div class="cam-preview-bar">
                    <button class="cam-preview-retake" onclick="retakeSnap()">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <polyline points="1 4 1 10 7 10" />
                            <path d="M3.51 15a9 9 0 1 0 .49-5" />
                        </svg>
                        Retake
                    </button>
                    <button class="cam-preview-use" onclick="useSnap()">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Use Photo
                    </button>
                </div>
            </div>
        </div>

        {{-- Controls --}}
        <div class="cam-controls" id="camControls">
            {{-- Zoom --}}
            <div class="cam-zoom-row">
                <button class="cam-zoom-minus" onclick="nudgeZoom(-0.5)">−</button>
                <input type="range" class="cam-zoom-slider" id="zoomSlider" min="1" max="5" step="0.1" value="1"
                    oninput="applyZoom(this.value)">
                <button class="cam-zoom-plus" onclick="nudgeZoom(0.5)">+</button>
                <span class="cam-zoom-label" id="zoomLabel">1.0×</span>
            </div>
            {{-- Shutter row --}}
            <div class="cam-shutter-row">
                {{-- Upload from gallery --}}
                <label class="cam-side-btn" title="Upload from gallery" style="cursor:pointer">
                    <input type="file" accept="image/*" class="hidden-file" onchange="handleFile(event)">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </label>

                {{-- Shutter --}}
                <button class="cam-shutter" onclick="takeSnap()" title="Capture">
                    <div class="cam-shutter-ring">
                        <div class="cam-shutter-disc"></div>
                    </div>
                </button>

                {{-- Grid toggle --}}
                <button class="cam-side-btn" id="gridBtn" onclick="toggleGrid()" title="Toggle grid">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <line x1="8" y1="3" x2="8" y2="21" />
                        <line x1="16" y1="3" x2="16" y2="21" />
                        <line x1="3" y1="8" x2="21" y2="8" />
                        <line x1="3" y1="16" x2="21" y2="16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const subcategories = @json($subcategories);
    </script>

    <script>
        const categorySelect = document.querySelector('select[name="category"]');
        const subcategorySelect = document.getElementById('subcategorySelect');

        categorySelect.addEventListener('change', function () {
            const categoryCode = this.value;

            // Clear old options
            subcategorySelect.innerHTML = '<option value="">Select subcategory…</option>';

            if (!categoryCode || !subcategories[categoryCode]) return;

            subcategories[categoryCode].forEach(sub => {
                let option = document.createElement('option');
                option.value = sub.code;
                option.textContent = sub.display_name;
                subcategorySelect.appendChild(option);
            });
        });
    </script>
    <script>
        /* ═══════════════════════════════════════════
           STATE
           ═══════════════════════════════════════════ */
        let camStream = null;
        let facing = 'environment';
        let zoomLevel = 1;
        let gridOn = false;
        let snapDataUrl = null;

        /* DOM refs */
        const camModal = document.getElementById('camModal');
        const camLive = document.getElementById('camLive');
        const camCanvas = document.getElementById('camCanvas');
        const camFlash = document.getElementById('camFlash');
        const camLoading = document.getElementById('camLoading');
        const camPreview = document.getElementById('camPreview');
        const camPreviewImg = document.getElementById('camPreviewImg');
        const camControls = document.getElementById('camControls');
        const camScan = document.getElementById('camScan');
        const zoomSlider = document.getElementById('zoomSlider');
        const zoomLabel = document.getElementById('zoomLabel');
        const gridOverlay = document.getElementById('camGridOverlay');
        const gridBtn = document.getElementById('gridBtn');
        const flipBtn = document.getElementById('flipBtn');
        const titleLabel = document.getElementById('camTitleLabel');

        const imgBadge = document.getElementById('imgBadge');
        const imgPreviewEl = document.getElementById('imgPreviewEl');
        const imgPlaceholder = document.getElementById('imgPlaceholder');
        const imgStatus = document.getElementById('imgStatus');
        const imageDataField = document.getElementById('imageDataField');

        /* ═══════════════════════════════════════════
           CAMERA MODAL
           ═══════════════════════════════════════════ */
        async function openCamModal() {
            camModal.classList.add('open');
            document.body.style.overflow = 'hidden';
            camLoading.classList.remove('hidden');
            camPreview.classList.remove('show');
            camControls.style.display = '';
            await startStream();
        }

        function closeCamModal() {
            stopStream();
            camModal.classList.remove('open');
            document.body.style.overflow = '';
        }

        async function startStream() {
            stopStream();
            camLoading.classList.remove('hidden');
            try {
                camStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: facing },
                        width: { ideal: 3840 },
                        height: { ideal: 2160 },
                    },
                    audio: false
                });
                camLive.srcObject = camStream;
                await camLive.play();

                /* Hardware zoom if available */
                const track = camStream.getVideoTracks()[0];
                const caps = track.getCapabilities?.() || {};
                if (caps.zoom) {
                    zoomSlider.min = caps.zoom.min;
                    zoomSlider.max = caps.zoom.max;
                    zoomSlider.step = caps.zoom.step || 0.1;
                    zoomSlider.value = caps.zoom.min;
                    zoomLevel = caps.zoom.min;
                    setZoomLabel(zoomLevel);
                } else {
                    zoomSlider.min = 1; zoomSlider.max = 5; zoomSlider.step = 0.1;
                    zoomSlider.value = 1; zoomLevel = 1;
                    setZoomLabel(1);
                }
                titleLabel.textContent = facing === 'environment' ? 'Back Camera' : 'Front Camera';
            } catch (e) {
                camLoading.innerHTML = '<p style="color:#fca5a5;padding:1.5rem;text-align:center;font-size:.9rem">Camera access denied or unavailable.<br><small style="opacity:.6">Use the "Upload File" button instead.</small></p>';
                return;
            }
            camLoading.classList.add('hidden');
        }

        function stopStream() {
            camStream?.getTracks().forEach(t => t.stop());
            camStream = null;
        }

        async function flipCamera() {
            facing = facing === 'environment' ? 'user' : 'environment';
            await startStream();
        }

        function applyZoom(val) {
            zoomLevel = parseFloat(val);
            setZoomLabel(zoomLevel);
            if (!camStream) return;
            const track = camStream.getVideoTracks()[0];
            const caps = track.getCapabilities?.() || {};
            if (caps.zoom) {
                track.applyConstraints({ advanced: [{ zoom: zoomLevel }] }).catch(() => { });
            } else {
                /* CSS scale fallback — zooms from centre */
                camLive.style.transform = `scale(${zoomLevel})`;
            }
        }

        function nudgeZoom(delta) {
            const s = zoomSlider;
            const nv = Math.min(parseFloat(s.max), Math.max(parseFloat(s.min), zoomLevel + delta));
            s.value = nv;
            applyZoom(nv);
        }

        function setZoomLabel(v) {
            zoomLabel.textContent = parseFloat(v).toFixed(1) + '×';
        }

        function toggleGrid() {
            gridOn = !gridOn;
            gridOverlay.classList.toggle('show', gridOn);
            gridBtn.classList.toggle('active', gridOn);
        }

        function takeSnap() {
            camCanvas.width = camLive.videoWidth || camLive.clientWidth;
            camCanvas.height = camLive.videoHeight || camLive.clientHeight;
            const ctx = camCanvas.getContext('2d');
            if (facing === 'user') { ctx.translate(camCanvas.width, 0); ctx.scale(-1, 1); }
            ctx.drawImage(camLive, 0, 0, camCanvas.width, camCanvas.height);

            /* Flash */
            camFlash.classList.remove('firing');
            void camFlash.offsetWidth;
            camFlash.classList.add('firing');

            snapDataUrl = camCanvas.toDataURL('image/jpeg', 0.92);
            camPreviewImg.src = snapDataUrl;
            camPreview.classList.add('show');
            camControls.style.display = 'none';
        }

        function retakeSnap() {
            snapDataUrl = null;
            camPreview.classList.remove('show');
            camControls.style.display = '';
        }

        function useSnap() {
            if (!snapDataUrl) return;
            commitImage(snapDataUrl, 'Camera photo');
            closeCamModal();
        }

        /* Pinch-to-zoom */
        let lastPinchDist = null;
        document.getElementById('camViewfinder').addEventListener('touchmove', e => {
            if (e.touches.length !== 2) return;
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            const d = Math.sqrt(dx * dx + dy * dy);
            if (lastPinchDist !== null) {
                const s = zoomSlider;
                const nv = Math.min(parseFloat(s.max), Math.max(parseFloat(s.min), zoomLevel + (d - lastPinchDist) / 120));
                s.value = nv;
                applyZoom(nv);
            }
            lastPinchDist = d;
        }, { passive: true });
        document.getElementById('camViewfinder').addEventListener('touchend', () => { lastPinchDist = null; });

        /* ═══════════════════════════════════════════
           FILE UPLOAD
           ═══════════════════════════════════════════ */
        function handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;

            /* Close camera modal if open */
            if (camModal.classList.contains('open')) closeCamModal();

            const reader = new FileReader();
            reader.onload = ev => commitImage(ev.target.result, file.name);
            reader.readAsDataURL(file);
            /* Reset so same file can be re-selected */
            e.target.value = '';
        }

        /* ═══════════════════════════════════════════
           SHARED: set the image everywhere
           ═══════════════════════════════════════════ */
        function commitImage(dataUrl, label) {
            imageDataField.value = dataUrl;

            /* Show preview */
            imgPreviewEl.src = dataUrl;
            imgPreviewEl.classList.add('visible');
            imgPlaceholder.style.display = 'none';

            /* Badge */
            imgBadge.textContent = '✓ Photo ready';
            imgBadge.className = 'img-badge has-img';

            /* Status bar */
            imgStatus.innerHTML = `
                            <span class="dot"></span>
                            <span style="font-size:.75rem;color:var(--muted)">${label}</span>
                            <button class="remove-btn" onclick="removeImage()">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Remove
                            </button>`;
        }

        function removeImage() {
            imageDataField.value = '';
            imgPreviewEl.src = '';
            imgPreviewEl.classList.remove('visible');
            imgPlaceholder.style.display = '';
            imgBadge.textContent = 'No photo';
            imgBadge.className = 'img-badge';
            imgStatus.innerHTML = '<span style="color:var(--muted);font-size:.75rem">Supports JPG, PNG, WEBP</span>';
        }
    </script>

    <script>
        document.getElementById('inventoryForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let form = this;
            let button = document.getElementById('submitBtn');
            let text = button.querySelector('.btn-text');

            Swal.fire({
                title: 'Save Item?',
                text: "Do you want to add this item to inventory?",
                icon: 'question',
                background: '#fff8e1', // light yellow
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Disable button + change text
                    button.disabled = true;
                    text.innerText = 'Saving...';

                    form.submit();
                }
            });
        });
    </script>

@endpush