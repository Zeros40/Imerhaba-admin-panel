import puppeteer, { Browser } from 'puppeteer';

let browser: Browser | null = null;

async function getBrowser(): Promise<Browser> {
  if (!browser) {
    browser = await puppeteer.launch({
      headless: true,
      args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });
  }
  return browser;
}

export async function scrapeWebsite(url: string, timeoutMs = 30000): Promise<string> {
  try {
    // Validate URL
    const urlObj = new URL(url);
    const targetUrl = urlObj.toString();

    const browser = await getBrowser();
    const page = await browser.newPage();

    // Set timeout
    page.setDefaultTimeout(timeoutMs);
    page.setDefaultNavigationTimeout(timeoutMs);

    // Navigate to URL
    await page.goto(targetUrl, {
      waitUntil: 'networkidle2',
    });

    // Wait for content to load
    await page.waitForSelector('body', { timeout: 5000 }).catch(() => {});

    // Extract HTML
    const html = await page.content();

    // Extract metadata
    const metadata = await page.evaluate(() => {
      const metas = {
        title: document.title,
        description: document.querySelector('meta[name="description"]')?.getAttribute('content') || '',
        keywords: document.querySelector('meta[name="keywords"]')?.getAttribute('content') || '',
        ogTitle: document.querySelector('meta[property="og:title"]')?.getAttribute('content') || '',
        ogDescription: document.querySelector('meta[property="og:description"]')?.getAttribute('content') || '',
        ogImage: document.querySelector('meta[property="og:image"]')?.getAttribute('content') || '',
        canonical: document.querySelector('link[rel="canonical"]')?.getAttribute('href') || '',
      };
      return metas;
    });

    // Clean up
    await page.close();

    // Combine HTML and metadata
    const content = JSON.stringify({
      html,
      metadata,
      url: targetUrl,
      extractedAt: new Date().toISOString(),
    });

    return content;
  } catch (error) {
    console.error(`Scraping error for ${url}:`, error);
    throw new Error(`Failed to scrape website: ${error instanceof Error ? error.message : 'Unknown error'}`);
  }
}

export async function closeBrowser(): Promise<void> {
  if (browser) {
    await browser.close();
    browser = null;
  }
}

export async function scrapeMultiple(urls: string[]): Promise<Record<string, string>> {
  const results: Record<string, string> = {};

  for (const url of urls) {
    try {
      results[url] = await scrapeWebsite(url);
    } catch (error) {
      console.error(`Failed to scrape ${url}:`, error);
      results[url] = '';
    }
  }

  return results;
}
