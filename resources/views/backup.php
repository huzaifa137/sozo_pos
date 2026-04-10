@if ($errors->any())
    <div class="error-alert">
        <div class="error-alert-icon">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill="#b91c1c"/>
            </svg>
        </div>
        <div class="error-alert-content">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <style>
        .error-alert {
            display: flex;
            align-items: flex-start;
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .error-alert-icon {
            flex-shrink: 0;
            margin-right: 0.75rem;
        }
        .error-alert-content {
            color: #991b1b;
            font-size: 0.875rem;
        }
        .error-alert-content strong {
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }
        .error-alert-content ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-top: 0.25rem;
            margin-bottom: 0;
        }
        .error-alert-content li {
            margin-top: 0.125rem;
        }
    </style>
@endif