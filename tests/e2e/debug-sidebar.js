const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

async function runTests() {
  console.log('Starting sidebar debug test...\n');
  
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  // Login
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle' });
  await page.fill('input[name="email"]', 'admin@vet.com');
  await page.fill('input[name="password"]', 'admin123');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(2000);
  
  console.log('Logged in, URL:', page.url());
  
  // Get full sidebar HTML
  const sidebarHTML = await page.evaluate(() => {
    return document.querySelector('.sidebar').innerHTML;
  });
  console.log('\nFull sidebar HTML:');
  console.log(sidebarHTML.substring(0, 5000));
  
  // Count all links
  const linkCount = await page.evaluate(() => {
    return document.querySelectorAll('.sidebar a').length;
  });
  console.log('\nTotal links in sidebar:', linkCount);
  
  // List all links
  const links = await page.evaluate(() => {
    return Array.from(document.querySelectorAll('.sidebar a')).map(a => ({
      href: a.href,
      text: a.textContent.trim().substring(0, 30)
    }));
  });
  console.log('\nAll links:', links);
  
  await browser.close();
  console.log('\nDone');
}

runTests().catch(console.error);
