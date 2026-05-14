const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

async function runTests() {
  console.log('Testing button hrefs...\n');
  
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  // Login
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', 'admin@vet.com');
  await page.fill('input[name="password"]', 'admin123');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(1000);
  
  const pages = ['/tutors', '/pets', '/users', '/appointments', '/services'];
  
  for (const pg of pages) {
    console.log(`\n=== ${pg} ===`);
    await page.goto(`${BASE_URL}${pg}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(1000);
    
    // Get all action button hrefs
    const actionButtons = await page.evaluate(() => {
      const buttons = document.querySelectorAll('.btn-action');
      return Array.from(buttons).map(btn => ({
        href: btn.href || btn.closest('a')?.href || 'N/A',
        icon: btn.querySelector('i')?.className || 'N/A',
        type: btn.tagName
      }));
    });
    
    console.log('Action buttons found:', actionButtons.length);
    actionButtons.forEach((btn, i) => {
      console.log(`  ${i + 1}. ${btn.type} - ${btn.href} - ${btn.icon}`);
    });
    
    // Check Create button
    const createBtn = await page.locator('a[href*="/create"]').count();
    console.log(`Create buttons: ${createBtn}`);
    
    // Check for edit links
    const editLinks = await page.evaluate(() => {
      return Array.from(document.querySelectorAll('a')).filter(a => a.href.includes('/edit')).map(a => a.href);
    });
    console.log(`Edit links: ${editLinks.length}`, editLinks.slice(0, 2));
    
    // Check for show links
    const showLinks = await page.evaluate(() => {
      return Array.from(document.querySelectorAll('a')).filter(a => a.href.includes('/show')).map(a => a.href);
    });
    console.log(`Show links: ${showLinks.length}`, showLinks.slice(0, 2));
  }
  
  await browser.close();
  console.log('\nDone');
}

runTests().catch(console.error);
