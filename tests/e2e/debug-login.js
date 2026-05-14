const { chromium } = require('playwright');

const BASE_URL = 'http://localhost:8000';

async function runTests() {
  console.log('Starting login debug test...\n');
  
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  // Listen for console messages
  page.on('console', msg => {
    if (msg.type() === 'error') {
      console.log('Console Error:', msg.text());
    }
  });
  
  // Listen for page errors
  page.on('pageerror', error => {
    console.log('Page Error:', error.message);
  });
  
  console.log('Navigating to login page...');
  await page.goto(`${BASE_URL}/login`);
  await page.waitForTimeout(1000);
  
  console.log('Page title:', await page.title());
  
  // Fill login form
  console.log('\nFilling login form...');
  await page.fill('input[name="email"]', 'admin@vet.com');
  await page.fill('input[name="password"]', 'password');
  
  // Get CSRF token
  const csrfToken = await page.locator('meta[name="csrf-token"]').getAttribute('content');
  console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
  
  // Take screenshot before submit
  console.log('\nSubmitting login...');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(3000);
  
  const currentUrl = page.url();
  console.log('Current URL after login:', currentUrl);
  
  // Check for error messages
  const errorMessage = await page.locator('.alert-danger').textContent().catch(() => null);
  if (errorMessage) {
    console.log('Error message found:', errorMessage);
  }
  
  // Check page content
  const bodyText = await page.locator('body').textContent();
  console.log('\nPage content preview:', bodyText.substring(0, 500));
  
  await browser.close();
  
  console.log('\n========== END ==========');
}

runTests().catch(console.error);
