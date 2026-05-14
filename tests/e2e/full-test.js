const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

const USERS = [
    { email: 'admin@vet.com', password: 'admin123', role: 'Admin', menus: ['dashboard', 'tutors', 'pets', 'appointments', 'medical-records', 'vaccinations', 'exams', 'surgeries', 'prescriptions', 'invoices', 'reports', 'products', 'stock', 'suppliers', 'convenios', 'users', 'roles', 'services', 'categories'] },
    { email: 'vet@vet.com', password: 'vet123', role: 'Veterinario', menus: ['dashboard', 'tutors', 'pets', 'appointments', 'medical-records', 'vaccinations', 'exams', 'surgeries', 'prescriptions'] },
    { email: 'recep@vet.com', password: 'recep123', role: 'Recepcionista', menus: ['dashboard', 'tutors', 'pets', 'appointments', 'vaccinations'] },
    { email: 'financeiro@vet.com', password: 'fin123', role: 'Financeiro', menus: ['dashboard', 'invoices', 'reports'] },
    { email: 'estoque@vet.com', password: 'est123', role: 'Estoque', menus: ['dashboard', 'products', 'stock', 'suppliers'] },
];

const ROUTES = {
    'dashboard': '/dashboard',
    'tutors': '/tutors',
    'pets': '/pets',
    'appointments': '/appointments',
    'medical-records': '/medical-records',
    'vaccinations': '/vaccinations',
    'exams': '/exams',
    'surgeries': '/surgeries',
    'prescriptions': '/prescriptions',
    'invoices': '/invoices',
    'reports': '/reports/financial',
    'products': '/products',
    'stock': '/stock/movements',
    'suppliers': '/suppliers',
    'convenios': '/convenios',
    'users': '/users',
    'roles': '/roles',
    'services': '/services',
    'categories': '/categories',
};

async function login(page, email, password) {
    try {
        await page.goto(`${BASE_URL}/login`);
        await page.fill('input[name="email"]', email);
        await page.fill('input[name="password"]', password);
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard**', { timeout: 10000 });
        return true;
    } catch (e) {
        return false;
    }
}

async function logout(page) {
    try {
        await page.click('a[href*="logout"]');
        await page.waitForURL('**/login**', { timeout: 5000 });
    } catch (e) {
        // Try form submission
        try {
            await page.evaluate(() => {
                const forms = document.querySelectorAll('form');
                forms.forEach(f => {
                    if (f.action && f.action.includes('logout')) f.submit();
                });
            });
        } catch (err) {}
    }
}

async function testPage(page, route, name) {
    try {
        const response = await page.goto(`${BASE_URL}${route}`, { timeout: 15000, waitUntil: 'networkidle' });
        const status = response ? response.status() : 'N/A';
        const title = await page.title();
        const hasError = await page.locator('.text-red-500, .alert-danger, .error, .exception').count() > 0;
        
        return {
            route,
            name,
            status,
            title,
            hasError,
            success: status === 200 && !hasError
        };
    } catch (e) {
        return {
            route,
            name,
            status: 'Error',
            title: 'N/A',
            hasError: true,
            success: false,
            error: e.message.substring(0, 100)
        };
    }
}

async function runTests() {
    console.log('🚀 Starting VetEssence E2E Tests\n');
    console.log('='.repeat(80));
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    // Enable console log capture
    const errors = [];
    page.on('console', msg => {
        if (msg.type() === 'error') {
            errors.push({ page: 'current', message: msg.text() });
        }
    });
    page.on('pageerror', err => {
        errors.push({ page: 'current', message: err.message });
    });
    
    const results = {};
    
    for (const user of USERS) {
        console.log(`\n👤 Testing: ${user.role} (${user.email})`);
        console.log('-'.repeat(60));
        
        results[user.role] = { passed: 0, failed: 0, pages: [] };
        
        const loggedIn = await login(page, user.email, user.password);
        if (!loggedIn) {
            console.log('❌ Login failed');
            continue;
        }
        console.log('✓ Logged in successfully');
        
        for (const menu of user.menus) {
            const route = ROUTES[menu];
            if (!route) continue;
            
            // Clear errors for this page
            errors.length = 0;
            
            const result = await testPage(page, route, menu);
            result.errors = [...errors];
            results[user.role].pages.push(result);
            
            if (result.success) {
                results[user.role].passed++;
                console.log(`  ✓ ${menu.padEnd(20)} [${result.status}]`);
            } else {
                results[user.role].failed++;
                console.log(`  ❌ ${menu.padEnd(20)} [${result.status}] - ${result.error || 'Has errors'}`);
            }
        }
        
        await logout(page);
    }
    
    await browser.close();
    
    // Print summary
    console.log('\n' + '='.repeat(80));
    console.log('📊 SUMMARY');
    console.log('='.repeat(80));
    
    let totalPassed = 0;
    let totalFailed = 0;
    
    for (const [role, data] of Object.entries(results)) {
        console.log(`\n${role}:`);
        console.log(`  Passed: ${data.passed} | Failed: ${data.failed}`);
        
        if (data.failed > 0) {
            console.log('  Failed pages:');
            for (const page of data.pages) {
                if (!page.success) {
                    console.log(`    - ${page.name}: ${page.error || page.status}`);
                }
            }
        }
        
        totalPassed += data.passed;
        totalFailed += data.failed;
    }
    
    console.log('\n' + '='.repeat(80));
    console.log(`TOTAL: ${totalPassed} passed | ${totalFailed} failed`);
    console.log('='.repeat(80));
    
    if (totalFailed > 0) {
        console.log('\n🔧 Pages with errors need fixing.');
        process.exit(1);
    } else {
        console.log('\n✅ All tests passed!');
        process.exit(0);
    }
}

runTests().catch(e => {
    console.error('Test runner error:', e);
    process.exit(1);
});
