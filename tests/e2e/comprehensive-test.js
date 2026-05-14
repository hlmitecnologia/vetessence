const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

const USERS = [
    { email: 'admin@vet.com', password: 'admin123', role: 'Admin' },
    { email: 'vet@vet.com', password: 'vet123', role: 'Veterinario' },
    { email: 'recep@vet.com', password: 'recep123', role: 'Recepcionista' },
    { email: 'financeiro@vet.com', password: 'fin123', role: 'Financeiro' },
    { email: 'estoque@vet.com', password: 'est123', role: 'Estoque' },
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

async function testPageLinks(page, route, name) {
    const results = [];
    const errors = [];
    
    try {
        // Track console errors
        page.on('pageerror', err => errors.push(err.message));
        
        const response = await page.goto(`${BASE_URL}${route}`, { timeout: 15000, waitUntil: 'networkidle' });
        const status = response ? response.status() : 'N/A';
        
        if (status !== 200) {
            return [{ route, name, action: 'page_load', status, success: false }];
        }
        
        results.push({ route, name, action: 'page_load', status: 200, success: true });
        
        // Test sidebar menu links
        const sidebarLinks = await page.locator('.sidebar .nav-link[href]').all();
        for (const link of sidebarLinks.slice(0, 5)) { // Test first 5 sidebar links
            const href = await link.getAttribute('href');
            const text = await link.innerText().catch(() => 'link');
            try {
                const linkResponse = await page.goto(`${BASE_URL}${href}`, { timeout: 10000, waitUntil: 'domcontentloaded' });
                results.push({ route, name, action: `sidebar:${text.trim().substring(0, 20)}`, status: linkResponse.status(), success: linkResponse.status() === 200 });
                await page.goBack();
            } catch (e) {
                results.push({ route, name, action: `sidebar:${text.trim().substring(0, 20)}`, status: 'Error', success: false, error: e.message.substring(0, 50) });
            }
        }
        
        // Test DataTable pagination if exists
        const pagination = await page.locator('.dataTables_paginate .paginate_button').count();
        if (pagination > 0) {
            results.push({ route, name, action: 'pagination', status: 'N/A', success: true, note: `${pagination} pages` });
        }
        
        // Test table action buttons (visualizar, editar)
        const viewButtons = await page.locator('a[title="Visualizar"], a.btn-info').count();
        const editButtons = await page.locator('a[title="Editar"], a.btn-primary').count();
        const deleteButtons = await page.locator('button[title="Excluir"], button.btn-danger, form button.btn-danger').count();
        
        if (viewButtons > 0) {
            results.push({ route, name, action: 'view_buttons', status: `${viewButtons} found`, success: true });
        }
        if (editButtons > 0) {
            results.push({ route, name, action: 'edit_buttons', status: `${editButtons} found`, success: true });
        }
        if (deleteButtons > 0) {
            results.push({ route, name, action: 'delete_buttons', status: `${deleteButtons} found`, success: true });
        }
        
        // Test clicking a view button if exists
        const viewBtn = page.locator('a[title="Visualizar"], a.btn-info').first();
        if (await viewBtn.count() > 0) {
            try {
                await viewBtn.click();
                await page.waitForLoadState('domcontentloaded', { timeout: 5000 });
                const showStatus = page.url().includes('/show') || page.url().includes('/edit') ? 200 : 'N/A';
                results.push({ route, name, action: 'click_view', status: showStatus, success: page.url() !== `${BASE_URL}${route}` });
                await page.goBack();
            } catch (e) {
                results.push({ route, name, action: 'click_view', status: 'Error', success: false });
            }
        }
        
        // Test clicking an edit button if exists
        const editBtn = page.locator('a[title="Editar"], a.btn-primary').first();
        if (await editBtn.count() > 0) {
            try {
                await editBtn.click();
                await page.waitForLoadState('domcontentloaded', { timeout: 5000 });
                const editStatus = page.url().includes('/edit') ? 200 : 'N/A';
                results.push({ route, name, action: 'click_edit', status: editStatus, success: page.url() !== `${BASE_URL}${route}` });
                await page.goBack();
            } catch (e) {
                results.push({ route, name, action: 'click_edit', status: 'Error', success: false });
            }
        }
        
        // Test "Novo" button if exists
        const novoBtn = page.locator('a:has-text("Novo"), a:has-text("Nova"), a:has-text("Novo"), button:has-text("Novo")').first();
        if (await novoBtn.count() > 0) {
            try {
                await novoBtn.click();
                await page.waitForLoadState('domcontentloaded', { timeout: 5000 });
                results.push({ route, name, action: 'click_novo', status: 200, success: page.url() !== `${BASE_URL}${route}` });
                await page.goBack();
            } catch (e) {
                results.push({ route, name, action: 'click_novo', status: 'Error', success: false });
            }
        }
        
        // Test search form if exists
        const searchInput = page.locator('input[name="search"], input[type="search"]').first();
        if (await searchInput.count() > 0) {
            results.push({ route, name, action: 'search_input', status: 'found', success: true });
        }
        
        // Test filter dropdowns if exist
        const filterSelects = await page.locator('select[name]').count();
        if (filterSelects > 0) {
            results.push({ route, name, action: 'filter_selects', status: `${filterSelects} found`, success: true });
        }
        
        // Test card-tools buttons
        const cardTools = await page.locator('.card-tools a, .card-tools button').count();
        if (cardTools > 0) {
            results.push({ route, name, action: 'card_tools', status: `${cardTools} buttons`, success: true });
        }
        
    } catch (e) {
        results.push({ route, name, action: 'page_load', status: 'Error', success: false, error: e.message.substring(0, 100) });
    }
    
    return results;
}

async function runTests() {
    console.log('🚀 Starting Comprehensive E2E Tests\n');
    console.log('='.repeat(80));
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    const allResults = {};
    
    for (const user of USERS) {
        console.log(`\n👤 Testing: ${user.role} (${user.email})`);
        console.log('-'.repeat(60));
        
        allResults[user.role] = [];
        
        const loggedIn = await login(page, user.email, user.password);
        if (!loggedIn) {
            console.log('❌ Login failed');
            continue;
        }
        console.log('✓ Logged in\n');
        
        for (const [key, route] of Object.entries(ROUTES)) {
            process.stdout.write(`  Testing ${key.padEnd(20)}...`);
            const results = await testPageLinks(page, route, key);
            allResults[user.role].push(...results);
            
            const passed = results.filter(r => r.success).length;
            const failed = results.filter(r => !r.success).length;
            console.log(` ${passed}✓ ${failed > 0 ? failed + '❌' : ''}`);
            
            if (failed > 0) {
                const failedItems = results.filter(r => !r.success);
                for (const item of failedItems) {
                    console.log(`     - ${item.action}: ${item.status} ${item.error || ''}`);
                }
            }
        }
        
        await logout(page);
    }
    
    await browser.close();
    
    // Print summary
    console.log('\n' + '='.repeat(80));
    console.log('📊 DETAILED SUMMARY');
    console.log('='.repeat(80));
    
    let totalPassed = 0;
    let totalFailed = 0;
    
    for (const [role, results] of Object.entries(allResults)) {
        const passed = results.filter(r => r.success).length;
        const failed = results.filter(r => !r.success).length;
        totalPassed += passed;
        totalFailed += failed;
        
        console.log(`\n${role}: ${passed} passed | ${failed} failed`);
        
        if (failed > 0) {
            console.log('  Failed items:');
            const failedItems = results.filter(r => !r.success);
            for (const item of failedItems.slice(0, 10)) {
                console.log(`    - ${item.name}/${item.action}: ${item.status}`);
            }
            if (failedItems.length > 10) {
                console.log(`    ... and ${failedItems.length - 10} more`);
            }
        }
    }
    
    console.log('\n' + '='.repeat(80));
    console.log(`TOTAL: ${totalPassed} passed | ${totalFailed} failed`);
    console.log('='.repeat(80));
    
    if (totalFailed > 0) {
        console.log('\n🔧 Some tests failed. Fix required.');
        process.exit(1);
    } else {
        console.log('\n✅ All comprehensive tests passed!');
        process.exit(0);
    }
}

runTests().catch(e => {
    console.error('Test runner error:', e);
    process.exit(1);
});
