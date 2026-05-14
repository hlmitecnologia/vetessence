const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

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

async function testPage(page, route, name) {
    try {
        const response = await page.goto(`${BASE_URL}${route}`, { timeout: 15000, waitUntil: 'domcontentloaded' });
        const status = response ? response.status() : 'N/A';
        const bodyText = await page.locator('body').innerText();
        const errorMatch = bodyText.match(/SQLSTATE.*?(?=\n|$)/);
        const error = errorMatch ? errorMatch[0].substring(0, 150) : null;
        
        // Check for Laravel error page
        const hasLaravelError = await page.locator('.exception-message, .exception, .bg-red, .text-red-600').count() > 0;
        
        return {
            route,
            name,
            status,
            error,
            hasLaravelError,
            success: status === 200 && !hasLaravelError
        };
    } catch (e) {
        return {
            route,
            name,
            status: 'Error',
            error: e.message.substring(0, 200)
        };
    }
}

async function runTests() {
    console.log('🔍 Testing pages to identify errors...\n');
    
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    // Login
    const loggedIn = await login(page, 'admin@vet.com', 'admin123');
    if (!loggedIn) {
        console.log('Login failed!');
        process.exit(1);
    }
    console.log('✓ Logged in as Admin\n');
    
    const routes = [
        { route: '/dashboard', name: 'dashboard' },
        { route: '/tutors', name: 'tutors' },
        { route: '/pets', name: 'pets' },
        { route: '/appointments', name: 'appointments' },
        { route: '/medical-records', name: 'medical-records' },
        { route: '/vaccinations', name: 'vaccinations' },
        { route: '/exams', name: 'exams' },
        { route: '/surgeries', name: 'surgeries' },
        { route: '/reports/financial', name: 'reports' },
        { route: '/users', name: 'users' },
    ];
    
    for (const { route, name } of routes) {
        const result = await testPage(page, route, name);
        if (!result.success) {
            console.log(`❌ ${name} [${result.status}]`);
            if (result.error) {
                console.log(`   Error: ${result.error}`);
            }
        } else {
            console.log(`✓ ${name} [${result.status}]`);
        }
    }
    
    await browser.close();
}

runTests().catch(console.error);
