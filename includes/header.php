<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Swimming Club</title>
    <link rel="icon" type="image/png" href="../assets/favicon.png?v=<?= time() ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Global Dashboard palette overridden to Navy/Cyan
                        algolia: {
                            blue: '#0891b2', // cyan-600
                            darkblue: '#0e7490', // cyan-700
                            navy: '#0f172a', // slate-900
                            sidebar: '#0f172a', // slate-900
                            hover: '#0891b2', // cyan-600
                        },
                        panel: {
                            bg: '#FFFFFF',
                            surface: '#f8fafc',
                            border: '#e2e8f0',
                            hover: '#f1f5f9',
                        }
                    },
                    fontFamily: {
                        'inter': ['Plus Jakarta Sans', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc;
            color: #0f172a;
        }
        
        /* Sidebar */
        .sidebar-nav {
            background: #0f172a;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            border-radius: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
            position: relative;
            margin: 2px 0;
        }
        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar-link.active {
            color: #22d3ee;
            background: rgba(34, 211, 238, 0.1);
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 4px;
            bottom: 4px;
            width: 4px;
            background: #06b6d4;
            border-radius: 0 4px 4px 0;
        }
        .sidebar-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 8px 16px 4px;
            margin-top: 16px;
        }
        
        /* Cards */
        .card {
            background: #FFFFFF;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .card-hover:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        /* Stat Cards Number Weight */
        .stat-number {
            font-weight: 800;
        }
        
        /* Buttons */
        button, .btn, a.badge {
            border-radius: 0.5rem !important; /* rounded-lg */
        }
        
        /* Modern Tables */
        table { border-collapse: separate; border-spacing: 0; width: 100%; border-radius: 0.75rem; overflow: hidden; }
        th { background-color: #f8fafc; font-weight: 700 !important; color: #475569 !important; padding: 1rem 1.25rem !important; border-bottom: 1px solid #e2e8f0; }
        td { padding: 1rem 1.25rem !important; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tbody tr:hover { background-color: #f8fafc; }
        tbody tr:nth-child(even) { background-color: #fcfcfd; }

        
        /* Topbar */
        .topbar {
            background: #FFFFFF;
            border-bottom: 1px solid #E8E8EF;
        }
        
        /* Search box (Algolia style) */
        .search-box {
            background: #F5F5FA;
            border: 1px solid #E8E8EF;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            color: #9CA0B8;
            transition: all 0.15s ease;
        }
        .search-box:focus {
            border-color: #5468FF;
            box-shadow: 0 0 0 3px rgba(84,104,255,0.1);
            outline: none;
            color: #21243D;
        }
        
        /* Buttons */
        .btn-primary {
            background: #5468FF;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 18px;
            border-radius: 6px;
            transition: all 0.15s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: #3A4DC7;
        }
        .btn-outline {
            background: #FFFFFF;
            color: #21243D;
            font-weight: 500;
            font-size: 13px;
            padding: 8px 18px;
            border-radius: 6px;
            border: 1px solid #E8E8EF;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-outline:hover {
            border-color: #D1D5DB;
            background: #FAFAFE;
        }
        
        /* Table */
        .table-algolia { width: 100%; border-collapse: collapse; }
        .table-algolia thead th {
            font-size: 11px;
            font-weight: 600;
            color: #6B6F8D;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid #E8E8EF;
            text-align: left;
            background: #FAFAFE;
        }
        .table-algolia tbody td {
            font-size: 13px;
            padding: 12px 16px;
            border-bottom: 1px solid #F0F0F5;
            color: #4A4F6A;
        }
        .table-algolia tbody tr:hover {
            background: #FAFAFE;
        }
        .table-algolia tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .badge-blue { background: #EEF0FF; color: #5468FF; }
        .badge-green { background: #ECFDF5; color: #059669; }
        .badge-red { background: #FEF2F2; color: #DC2626; }
        .badge-yellow { background: #FFFBEB; color: #D97706; }
        .badge-gray { background: #F3F4F6; color: #6B7280; }
        
        /* Stat card number */
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #21243D;
            line-height: 1;
        }
        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: #6B6F8D;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }
        
        /* Mobile overlay */
        .sidebar-overlay {
            background: rgba(0,0,0,0.4);
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Form input (Algolia style) */
        .input-algolia {
            background: #FFFFFF;
            border: 1px solid #E8E8EF;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 13px;
            color: #21243D;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            width: 100%;
        }
        .input-algolia:focus {
            border-color: #5468FF;
            box-shadow: 0 0 0 3px rgba(84,104,255,0.1);
            outline: none;
        }
        .input-algolia::placeholder {
            color: #9CA0B8;
        }
        
        /* Select */
        .select-algolia {
            background: #FFFFFF;
            border: 1px solid #E8E8EF;
            border-radius: 6px;
            padding: 9px 12px;
            font-size: 13px;
            color: #21243D;
            transition: border-color 0.15s ease;
            width: 100%;
            appearance: auto;
        }
        .select-algolia:focus {
            border-color: #5468FF;
            box-shadow: 0 0 0 3px rgba(84,104,255,0.1);
            outline: none;
        }
        
        /* Smooth page load */
        .page-content { animation: fadeUp 0.3s ease-out; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Modal dark overlay */
        .modal-overlay { background: rgba(33, 36, 61, 0.5); }
    </style>
</head>
<body class="bg-panel-surface text-algolia-navy font-inter antialiased min-h-screen">