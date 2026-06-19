<?php
// Ensure this works smoothly with any dynamic titles
$page_title = $page_title ?? "Swift SC - Admin Portal";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        }
        /* CSS Overrides to magically transform existing light theme tailwind classes to dark glassmorphism */
        body { background: transparent !important; color: #f8fafc !important; }
        .bg-slate-50, .bg-gray-50, .bg-slate-100 { background-color: transparent !important; }
        
        .bg-white { 
            background: rgba(30, 41, 59, 0.4) !important; 
            border-color: rgba(255, 255, 255, 0.1) !important; 
            backdrop-filter: blur(16px); 
            color: #f8fafc !important;
        }
        .text-slate-800 { color: #f8fafc !important; }
        .text-gray-700 { color: #cbd5e1 !important; }
        .text-slate-500 { color: #94a3b8 !important; }
        
        .bg-slate-800 { background: rgba(15, 23, 42, 0.6) !important; }
        .border-gray-100, .border-[#E8E8EF] { border-color: rgba(255,255,255,0.05) !important; }
        .divide-gray-100 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(255,255,255,0.05) !important; }
        
        td, th { border-color: rgba(255,255,255,0.05) !important; color: #e2e8f0 !important; }
        
        input, select, textarea {
            background-color: rgba(15, 23, 42, 0.6) !important;
            color: #fff !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        input::placeholder { color: #64748b !important; }
        
        tr.hover\:bg-slate-50:hover { background-color: rgba(255,255,255,0.05) !important; }
    </style>
</head>
<body class="bg-mesh min-h-screen relative overflow-x-hidden antialiased">
    <!-- Background Decorative Elements -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-blue-900 rounded-full mix-blend-multiply filter blur-[128px] opacity-40 animate-pulse"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-purple-900 rounded-full mix-blend-multiply filter blur-[128px] opacity-40 animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <?php include '../includes/sidebar.php'; ?>
