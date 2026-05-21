<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('debug-docs/{section}/{page?}', function (Request $request, $section, $page = null) {
    // Login as first user
    $user = App\Models\User::first();
    if ($user) Auth::login($user);
    
    $controller = app(App\Http\Controllers\DocController::class);
    $response = $controller->show($request, $section, $page);
    $html = $response->render();
    
    // Check for old pattern
    $hasOldCode = strpos($html, 'bindSvgLightbox') !== false ? 'NEW_CODE' : 'OLD_CODE';
    $hasScriptWrapper = preg_match('/<\/script>\s*<script>\s*document\.addEventListener/s', $html) ? 'HAS_NESTED_SCRIPT' : 'CLEAN';
    
    $lines = explode("\n", $html);
    $totalLines = count($lines);
    
    echo "<pre style='background:#fff;color:#333;font:12px monospace'>";
    echo "Total lines: $totalLines\n";
    echo "Lightbox code: $hasOldCode\n";
    echo "Script wrapper: $hasScriptWrapper\n\n";
    echo "--- Last 20 lines ---\n";
    for ($i = max(0, $totalLines - 20); $i < $totalLines; $i++) {
        $ln = $i + 1;
        $esc = htmlspecialchars($lines[$i]);
        echo "$ln: $esc\n";
    }
    echo "</pre>";
})->name('debug.docs');
