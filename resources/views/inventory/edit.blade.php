@extends('layouts.app')

@section('title', 'Edit — ' . $inventoryItem->name)

@push('styles')
{{-- Reuse the same styles as create --}}
<style>
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-family: var(--font-head); font-size: 2rem; font-weight: 800; letter-spacing: -.5px; }
    .page-header p { color: var(--muted); margin-top: .3rem; font-size: .95rem; }

    .form-grid { display: grid; grid-template-columns: 380px 1fr; gap: 2rem; align-items: start; }

    .camera-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden; position: sticky; top: 84px;
    }
    .camera-card-header { padding: 1rem 1.2rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .camera-card-header h3 { font-family: var(--font-head); font-weight: 700; font-size: 1rem; }
    .camera-badge { background: rgba(240,192,64,.12); color: var(--accent); font-size: .75rem; font-weight: 600; padding: .2rem .6rem; border-radius: 20px; border: 1px solid rgba(240,192,64,.25); }

    .camera-viewport { position: relative; width: 100%; aspect-ratio: 4/3; background: #000; overflow: hidden; }
    #cameraFeed, #previewCanvas { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    #previewCanvas { display: none; }
    .corner { position: absolute; width: 22px; height: 22px; border-color: var(--accent); border-style: solid; z-index: 5; }
    .corner-tl { top: 12px; left: 12px; border-width: 2px 0 0 2px; }
    .corner-tr { top: 12px; right: 12px; border-width: 2px 2px 0 0; }
    .corner-bl { bottom: 12px; left: 12px; border-width: 0 0 2px 2px; }
    .corner-br { bottom: 12px; right: 12px; border-width: 0 2px 2px 0; }
    .camera-overlay-msg { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .8rem; background: rgba(13,15,20,.85); z-index: 4; transition: opacity .3s; }
    .camera-overlay-msg.hidden { opacity: 0; pointer-events: none; }
    .camera-overlay-msg svg { color: var(--accent); }
    .camera-overlay-msg p { color: var(--muted); font-size: .85rem; }
    @keyframes scanLine { 0%{top:10%} 100%{top:90%} }
    .scan-line { position: absolute; left:0; right:0; height:1px; background: linear-gradient(90deg,transparent,var(--accent),transparent); z-index:3; animation: scanLine 2s ease-in-out infinite alternate; display:none; }
    .camera-live .scan-line { display: block; }
    @keyframes flash { 0%,100%{opacity:0} 50%{opacity:.8} }
    .shutter-flash { position:absolute; inset:0; background:#fff; z-index:10; pointer-events:none; opacity:0; }
    .shutter-flash.snap { animation: flash .25s ease; }

    .camera-controls { padding: 1rem; display: flex; gap: .7rem; justify-content: center; }
    .btn-camera { display:inline-flex; align-items:center; gap:.45rem; padding:.65rem 1.3rem; border-radius:8px; border:none; font-family:var(--font-body); font-weight:600; font-size:.88rem; cursor:pointer; transition:all .18s; }
    .btn-open-cam { background: var(--accent); color: #0d0f14; }
    .btn-open-cam:hover { background: #ffd55e; }
    .btn-capture { background: #fff; color: #0d0f14; }
    .btn-retake { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
    .btn-retake:hover { border-color: var(--accent); color: var(--accent); }
    .preview-label { padding:.6rem 1rem; font-size:.78rem; color:var(--muted); display:flex; align-items:center; gap:.4rem; border-top:1px solid var(--border); }
    .preview-label .dot { width:7px; height:7px; border-radius:50%; background:var(--success); animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }

    /* existing image */
    .existing-img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
    .existing-label { padding:.6rem 1rem; font-size:.78rem; color:var(--muted); border-top:1px solid var(--border); }

    .fields-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.8rem; }
    .fields-card h3 { font-family: var(--font-head); font-weight: 700; font-size: 1rem; margin-bottom: 1.4rem; padding-bottom: .9rem; border-bottom: 1px solid var(--border); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group.full { grid-column: 1 / -1; }
    label { display:block; font-size:.8rem; font-weight:600; color:var(--muted); margin-bottom:.45rem; text-transform:uppercase; letter-spacing:.05em; }
    input[type="text"], input[type="number"], textarea, select { width:100%; background:var(--bg); border:1px solid var(--border); color:var(--text); border-radius:8px; padding:.7rem .9rem; font-family:var(--font-body); font-size:.92rem; transition:border-color .18s,box-shadow .18s; outline:none; appearance:none; }
    input:focus, textarea:focus, select:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(240,192,64,.12); }
    select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .9rem center; padding-right:2.5rem; }
    
    /* Updated input-prefix styles - clean separation without pipeline */
    .input-prefix {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: border-color .18s, box-shadow .18s;
    }
    
    .input-prefix:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(240,192,64,.12);
    }
    
    .input-prefix span {
        padding: 0 0 0 12px;
        color: var(--muted);
        font-size: .88rem;
        font-weight: 500;
        pointer-events: none;
        margin-right: 12px;
    }
    
    .input-prefix input {
        flex: 1;
        background: transparent;
        border: none;
        padding: .7rem 12px .7rem 0;
        color: var(--text);
        font-family: var(--font-body);
        font-size: .92rem;
        outline: none;
    }
    
    .input-prefix input:focus {
        box-shadow: none;
    }
    
    .error-msg { color: #fca5a5; font-size: .8rem; margin-top: .3rem; }
    .form-actions { display:flex; gap:.8rem; padding-top:1.2rem; border-top:1px solid var(--border); margin-top:1.5rem; }
    .btn-submit { flex:1; background:var(--accent); color:#0d0f14; border:none; border-radius:8px; padding:.8rem 1.5rem; font-family:var(--font-head); font-size:1rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.5rem; transition:background .18s; }
    .btn-submit:hover { background: #ffd55e; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); padding: .8rem 1.5rem; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; transition: all .18s; }
    .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } .camera-card { position: static; } }
    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Edit Item</h1>
    <p>Update details for <strong>{{ $inventoryItem->name }}</strong></p>
</div>

<form action="{{ route('inventory.update', $inventoryItem) }}" method="POST" id="inventoryForm">
    @csrf @method('PUT')
    <input type="hidden" name="image_data" id="imageData">

    <div class="form-grid">

        {{-- ── LEFT: CAMERA / CURRENT IMAGE ── --}}
        <div class="camera-card" id="cameraCard">
            <div class="camera-card-header">
                <h3>Item Photo</h3>
                <span class="camera-badge" id="cameraBadge">
                    {{ $inventoryItem->image_path ? 'Current photo' : 'No photo' }}
                </span>
            </div>

            <div class="camera-viewport" id="cameraViewport">
                @if($inventoryItem->image_path)
                    <img src="{{ asset($inventoryItem->image_path) }}"
                    alt="{{ $inventoryItem->name }}"
                    id="existingImg"
                    style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                @endif
                <video id="cameraFeed" autoplay playsinline muted style="display:none"></video>
                <canvas id="previewCanvas"></canvas>
                <div class="scan-line"></div>
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                <div class="shutter-flash" id="shutterFlash"></div>
                <div class="camera-overlay-msg {{ $inventoryItem->image_path ? 'hidden' : '' }}" id="cameraOverlay">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <p>Click the camera button below</p>
                </div>
            </div>

            <div class="camera-controls" id="cameraControls">
                <button type="button" class="btn-camera btn-open-cam" id="btnOpenCam" onclick="openCamera()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    {{ $inventoryItem->image_path ? 'Replace Photo' : 'Open Camera' }}
                </button>
                <button type="button" class="btn-camera btn-capture" id="btnCapture" style="display:none" onclick="capturePhoto()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>
                    Snap
                </button>
                <button type="button" class="btn-camera btn-retake" id="btnRetake" style="display:none" onclick="retakePhoto()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-5"/></svg>
                    Retake
                </button>
            </div>

            <div class="preview-label" id="previewLabel" style="display:none">
                <span class="dot"></span>
                New photo captured
            </div>
        </div>

        {{-- ── RIGHT: FIELDS ── --}}
        <div class="fields-card">
            <h3>Product Details</h3>

            <div class="form-group full">
                <label for="name">Item Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $inventoryItem->name) }}" required>
                @error('name')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="selling_price">Selling Price *</label>
                    <div class="input-prefix">
                        <span>UGX</span>
                        <input type="number" name="selling_price" id="selling_price" step="0.01" min="0"
                               value="{{ old('selling_price', $inventoryItem->selling_price) }}" required>
                    </div>
                    @error('selling_price')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="buying_price">Buying Price *</label>
                    <div class="input-prefix">
                        <span>UGX</span>
                        <input type="number" name="buying_price" id="buying_price" step="0.01" min="0"
                               value="{{ old('buying_price', $inventoryItem->buying_price) }}" required>
                    </div>
                    @error('buying_price')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select name="category" id="category" required>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $inventoryItem->category) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="stock_number">Stock Number *</label>
                    <select name="stock_number" id="stock_number" required>
                        @foreach(\App\Http\Controllers\InventoryController::STOCK_NUMBERS as $key => $label)
                            <option value="{{ $key }}" {{ old('stock_number', $inventoryItem->stock_number) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('stock_number')<p class="error-msg">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" name="quantity" id="quantity" min="0"
                       value="{{ old('quantity', $inventoryItem->quantity) }}" required>
                @error('quantity')<p class="error-msg">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3">{{ old('description', $inventoryItem->description) }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('inventory.index') }}" class="btn btn-outline">Cancel</a>
<button type="submit" class="btn-submit" id="updateBtn">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                    </svg>
<span id="updateBtnText">Update Item</span>
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let stream = null;
const video      = document.getElementById('cameraFeed');
const canvas     = document.getElementById('previewCanvas');
const overlay    = document.getElementById('cameraOverlay');
const badge      = document.getElementById('cameraBadge');
const viewport   = document.getElementById('cameraViewport');
const flash      = document.getElementById('shutterFlash');
const previewLbl = document.getElementById('previewLabel');
const imageData  = document.getElementById('imageData');
const existingImg = document.getElementById('existingImg');
const btnOpen    = document.getElementById('btnOpenCam');
const btnCapture = document.getElementById('btnCapture');
const btnRetake  = document.getElementById('btnRetake');

async function openCamera() {
    if (existingImg) existingImg.style.display = 'none';
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        video.srcObject = stream;
        video.style.display = 'block';
        canvas.style.display = 'none';
        overlay.classList.add('hidden');
        viewport.classList.add('camera-live');
        btnOpen.style.display = 'none';
        btnCapture.style.display = 'inline-flex';
        badge.textContent = 'Live';
    } catch (err) { alert('Camera access denied.'); }
}

function capturePhoto() {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    flash.classList.remove('snap'); void flash.offsetWidth; flash.classList.add('snap');
    video.style.display = 'none';
    canvas.style.display = 'block';
    viewport.classList.remove('camera-live');
    stream.getTracks().forEach(t => t.stop()); stream = null;
    imageData.value = canvas.toDataURL('image/jpeg', 0.85);
    btnCapture.style.display = 'none';
    btnRetake.style.display = 'inline-flex';
    previewLbl.style.display = 'flex';
    badge.textContent = '✓ New photo';
}

function retakePhoto() {
    imageData.value = '';
    canvas.style.display = 'none';
    previewLbl.style.display = 'none';
    if (existingImg) existingImg.style.display = 'block';
    btnRetake.style.display = 'none';
    btnOpen.style.display = 'inline-flex';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('inventoryForm');
    const updateBtn = document.getElementById('updateBtn');
    const updateBtnText = document.getElementById('updateBtnText');

    let confirmedSubmit = false;

    form.addEventListener('submit', function (e) {
        if (confirmedSubmit) return;

        e.preventDefault();

        Swal.fire({
            title: 'Update Item?',
            text: 'Are you sure you want to save these changes?',
            icon: 'question',
            background: '#111',
            color: '#fff',
            confirmButtonColor: '#facc15',
            cancelButtonColor: '#333',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                confirmedSubmit = true;

                updateBtn.disabled = true;
                updateBtn.style.opacity = '0.7';
                updateBtnText.textContent = 'Updating...';

                form.submit();
            }
        });
    });

    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: '#111',
            color: '#fff',
            iconColor: '#facc15'
        });
    @endif
});
</script>
@endpush
