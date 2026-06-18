<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Swimming Club</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Algolia-inspired palette
                        xenon: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        electric: {
                            DEFAULT: '#1E59FF',
                            50:  '#EBF1FF',
                            100: '#D6E4FF',
                            200: '#ADC8FF',
                            300: '#85ADFF',
                            400: '#5C91FF',
                            500: '#1E59FF',
                            600: '#0040E6',
                            700: '#0033B8',
                            800: '#00268A',
                            900: '#001A5C',
                        },
                        navy: {
                            DEFAULT: '#0F172A',
                            50:  '#1E293B',
                            100: '#1A2332',
                            200: '#141D2B',
                            300: '#0F172A',
                            400: '#0C1322',
                            500: '#0A0F1A',
                            600: '#070B14',
                            700: '#05080E',
                            800: '#030508',
                            900: '#010203',
                        }
                    },
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', 'sans-serif'],
                        'sora': ['Sora', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow-blue': '0 0 20px rgba(30, 89, 255, 0.15)',
                        'glow-purple': '0 0 20px rgba(99, 102, 241, 0.15)',
                        'card': '0 1px 3px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.4)',
                        'card-hover': '0 10px 40px rgba(0,0,0,0.4)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'slide-right': 'slideRight 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideRight: {
                            '0%': { opacity: '0', transform: 'translateX(-10px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            background: #0F172A;
            color: #e2e8f0;
        }
        
        /* Glassmorphism panels */
        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .glass-card-solid {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Sidebar styles */
        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .sidebar-link:hover {
            background: rgba(30, 89, 255, 0.1);
            color: #5C91FF;
        }
        .sidebar-link.active {
            background: rgba(30, 89, 255, 0.15);
            color: #5C91FF;
            border-left: 3px solid #1E59FF;
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #1E59FF, #6366f1);
            border-radius: 0 2px 2px 0;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        /* Subtle background grid */
        .bg-grid {
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        /* Table dark styling */
        .table-dark thead { background: rgba(15, 23, 42, 0.8); }
        .table-dark tbody tr { 
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.15s ease;
        }
        .table-dark tbody tr:hover { background: rgba(30, 89, 255, 0.05); }
        
        /* Button glow effect */
        .btn-electric {
            background: linear-gradient(135deg, #1E59FF 0%, #4f46e5 100%);
            box-shadow: 0 2px 10px rgba(30, 89, 255, 0.3);
            transition: all 0.2s ease;
        }
        .btn-electric:hover {
            box-shadow: 0 4px 20px rgba(30, 89, 255, 0.5);
            transform: translateY(-1px);
        }
        
        /* Input dark styling */
        .input-dark {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            color: #e2e8f0;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-dark:focus {
            border-color: #1E59FF;
            box-shadow: 0 0 0 3px rgba(30, 89, 255, 0.15);
            outline: none;
        }
        .input-dark::placeholder { color: #64748b; }
        
        /* Badge styling */
        .badge-electric {
            background: rgba(30, 89, 255, 0.15);
            color: #5C91FF;
            border: 1px solid rgba(30, 89, 255, 0.3);
        }
        .badge-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        /* Smooth page transitions */
        .page-content {
            animation: fadeIn 0.3s ease-out;
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Stat card gradient borders */
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1E59FF, #6366f1, #1E59FF);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { background-position: 200% 0; }
            50% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-navy-300 text-slate-200 font-inter antialiased bg-grid min-h-screen">