const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';
const USERS = [
  { email: 'admin@vet.com', password: 'admin123', role: 'admin' },
  { email: 'vet@vet.com', password: 'vet123', role: 'veterinario' },
  { email: 'recep@vet.com', password: 'recep123', role: 'recepcionista' },
  { email: 'financeiro@vet.com', password: 'fin123', role: 'financeiro' },
  { email: 'tutor@vet.com', password: 'tutor123', role: 'tutor' },
];

async function login(page, user) {
  await page.context().clearCookies();
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', user.email);
  await page.fill('input[name="password"]', user.password);
  await page.click('button[type="submit"]');
  await page.waitForTimeout(1000);
}

async function testSidebarNavigation(page) {
  const results = [];
  
  const pages = [
    { url: '/tutors', name: 'Tutors' },
    { url: '/pets', name: 'Pets' },
    { url: '/appointments', name: 'Appointments' },
    { url: '/medical-records', name: 'Medical Records' },
    { url: '/vaccinations', name: 'Vaccinations' },
    { url: '/exams', name: 'Exams' },
    { url: '/surgeries', name: 'Surgeries' },
    { url: '/prescriptions', name: 'Prescriptions' },
    { url: '/invoices', name: 'Invoices' },
    { url: '/services', name: 'Services' },
    { url: '/categories', name: 'Categories' },
    { url: '/products', name: 'Products' },
    { url: '/suppliers', name: 'Suppliers' },
    { url: '/convenios', name: 'Convenios' },
    { url: '/stock/movements', name: 'Stock Movements' },
    { url: '/users', name: 'Users' },
    { url: '/roles', name: 'Roles' },
  ];

  for (const item of pages) {
    try {
      await page.goto(`${BASE_URL}${item.url}`, { waitUntil: 'networkidle', timeout: 10000 });
      await page.waitForTimeout(500);
      const url = page.url();
      const isOnPage = url.includes(item.url);
      results.push({ item: item.name, status: isOnPage ? 'OK' : 'ERROR', url });
    } catch (e) {
      results.push({ item: item.name, status: 'ERROR', error: e.message.substring(0, 50) });
    }
  }
  
  return results;
}

async function testActionButtons(page) {
  const results = [];
  
  const buttons = [
    { selector: 'a[href*="/create"]', name: 'Create Button' },
    { selector: '.btn-action', name: 'Action Button' },
  ];

  for (const btn of buttons) {
    try {
      const count = await page.locator(btn.selector).count();
      if (count > 0) {
        const firstBtn = page.locator(btn.selector).first();
        if (await firstBtn.isVisible({ timeout: 2000 })) {
          await firstBtn.click();
          await page.waitForTimeout(500);
          const url = page.url();
          results.push({ item: btn.name, status: 'OK', url });
          
          if (url.includes('/create') || url.includes('/edit') || url.match(/\/\d+$/)) {
            await page.goBack();
            await page.waitForTimeout(500);
          }
        } else {
          results.push({ item: btn.name, status: 'NOT_VISIBLE', count });
        }
      } else {
        results.push({ item: btn.name, status: 'NOT_FOUND', count: 0 });
      }
    } catch (e) {
      results.push({ item: btn.name, status: 'ERROR', error: e.message.substring(0, 50) });
    }
  }
  
  return results;
}

async function testDataTable(page) {
  const results = [];
  
  try {
    const table = page.locator('table').first();
    if (await table.isVisible({ timeout: 3000 })) {
      results.push({ item: 'DataTable', status: 'OK', note: 'Table visible' });
      
      const rows = await page.locator('tbody tr').count();
      results.push({ item: 'DataTable Rows', status: 'OK', count: rows });
      
      const pagination = await page.locator('.pagination').isVisible().catch(() => false);
      if (pagination) {
        results.push({ item: 'Pagination', status: 'OK', note: 'Present' });
      }
    }
  } catch (e) {
    results.push({ item: 'DataTable', status: 'ERROR', error: e.message });
  }
  
  return results;
}

async function runTests() {
  console.log('Starting comprehensive action tests...\n');
  
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  const allResults = {};
  
  for (const user of USERS) {
    console.log(`\n=== Testing with ${user.role} (${user.email}) ===`);
    
    try {
      await login(page, user);
      const currentUrl = page.url();
      
      if (currentUrl.includes('/login')) {
        console.log(`  Login failed for ${user.role}`);
        allResults[user.role] = { login: 'FAILED', tests: [] };
        continue;
      }
      
      console.log(`  Login: OK`);
      
      // Test sidebar navigation
      const navResults = await testSidebarNavigation(page);
      console.log(`  Navigation items tested: ${navResults.length}`);
      
      // Navigate to each page and test buttons/DataTable
      const buttonResults = [];
      const dataTableResults = [];
      
      const pages = ['/tutors', '/pets', '/users', '/appointments', '/services'];
      for (const pg of pages) {
        await page.goto(`${BASE_URL}${pg}`);
        await page.waitForTimeout(1000);
        
        const btns = await testActionButtons(page);
        buttonResults.push(...btns);
        
        const dt = await testDataTable(page);
        dataTableResults.push(...dt);
      }
      
      allResults[user.role] = {
        login: 'OK',
        navigation: navResults,
        buttons: buttonResults,
        dataTables: dataTableResults
      };
      
      console.log(`  Navigation: ${navResults.filter(r => r.status === 'OK').length}/${navResults.length} OK`);
      console.log(`  Buttons: ${buttonResults.filter(r => r.status === 'OK').length}/${buttonResults.length} OK`);
      
    } catch (e) {
      console.log(`  Error: ${e.message}`);
      allResults[user.role] = { login: 'ERROR', error: e.message };
    }
  }
  
  await browser.close();
  
  // Summary
  console.log('\n\n========== SUMMARY ==========');
  for (const [role, results] of Object.entries(allResults)) {
    const navOk = results.navigation ? results.navigation.filter(r => r.status === 'OK').length : 0;
    const navTotal = results.navigation ? results.navigation.length : 0;
    const btnOk = results.buttons ? results.buttons.filter(r => r.status === 'OK').length : 0;
    const btnTotal = results.buttons ? results.buttons.length : 0;
    
    console.log(`${role}: Login=${results.login}, Nav=${navOk}/${navTotal}, Buttons=${btnOk}/${btnTotal}`);
  }
  
  console.log('\n========== END ==========');
}

runTests().catch(console.error);
