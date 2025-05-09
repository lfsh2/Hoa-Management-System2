<style>
    body {
        background: rgba(15, 23, 42, 0.95);
        color: #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
    }
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .page-header h2 {
        color: #e2e8f0;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .form-container {
        background: rgba(30, 41, 59, 0.5);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        outline: none;
    }
    .form-control::placeholder {
        color: #64748b;
    }
    .form-select {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }
    .form-select:focus {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        outline: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        color: #ffffff;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
    }
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }
    .alert {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(239, 68, 68, 0.2);
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .alert-success {
        border-color: rgba(16, 185, 129, 0.2);
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        color: #94a3b8;
        text-decoration: none;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .back-link:hover {
        color: #e2e8f0;
        text-decoration: none;
    }
    .back-link i {
        margin-right: 0.5rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
    }
    .status-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .status-badge.inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .status-badge i {
        margin-right: 0.5rem;
    }
</style>
