const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const BASE = process.env.NEXO_URL || 'http://localhost:8092';

(async () => {
  fs.mkdirSync('artifacts', {recursive: true});
  const browser = await chromium.launch({headless: true});
  const page = await browser.newPage({viewport:{width:1440,height:1000}});
  const errors = [];
  const passed = [];
  page.on('pageerror', error => errors.push(error.message));
  function pass(name) { passed.push(name); console.log('PASS:', name); }
  try {
    await page.goto(BASE, {waitUntil:'networkidle'});
    await page.locator('img').evaluateAll(async images => {
      images.forEach(image => image.loading = 'eager');
      await Promise.all(images.map(image => image.decode()));
    });
    assert.equal(await page.locator('li.product').count(), 8);
    assert.equal(await page.locator('img').evaluateAll(images => images.filter(i => !i.complete || !i.naturalWidth).length), 0);
    await page.screenshot({path:'artifacts/home-desktop.png',fullPage:true});
    pass('homepage: 8 products and all images loaded');
    for (const width of [390, 768, 1440]) {
      await page.setViewportSize({width,height:900});
      assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth),true,`overflow at ${width}`);
      if (width === 390) {
        const sizes = await page.locator('li.product').first().boundingBox();
        assert.ok(sizes.width > 150, 'mobile product column too narrow');
        await page.locator('.menu-toggle').click();
        assert.equal(await page.locator('.menu-toggle').getAttribute('aria-expanded'),'true');
        await page.keyboard.press('Escape');
        assert.equal(await page.locator('.menu-toggle').getAttribute('aria-expanded'),'false');
        await page.screenshot({path:'artifacts/home-mobile.png',fullPage:true});
      }
    }
    pass('responsive: no overflow at 390/768/1440; mobile menu and Escape');
    await page.goto(BASE+'/categoria-produto/audio/',{waitUntil:'networkidle'});
    assert.equal(await page.locator('li.product').count(),3);
    pass('audio category filters products');
    await page.goto(BASE+'/?s=teclado&post_type=product',{waitUntil:'networkidle'});
    assert.match(await page.locator('body').innerText(), /Teclado Forma 75/);
    pass('product search');
    await page.goto(BASE+'/produto/headphone-studio-one/',{waitUntil:'networkidle'});
    assert.ok(await page.locator('.nexo-product-note').isVisible());
    await page.screenshot({path:'artifacts/product-desktop.png',fullPage:true});
    await page.setViewportSize({width:390,height:844});
    assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth),true,'product mobile overflow');
    await page.screenshot({path:'artifacts/product-mobile.png',fullPage:true});
    await page.setViewportSize({width:1440,height:1000});
    await page.locator('select[name="attribute_cor"]').selectOption({label:'Areia'});
    await page.waitForFunction(()=>!document.querySelector('.single_add_to_cart_button').classList.contains('disabled'));
    await page.locator('.single_add_to_cart_button').click();
    await page.waitForLoadState('networkidle');
    await page.goto(BASE+'/carrinho/',{waitUntil:'networkidle'});
    assert.match(await page.locator('body').innerText(), /Areia/);
    await page.screenshot({path:'artifacts/cart-desktop.png',fullPage:true});
    pass('variable product adds selected color to cart');
    await page.locator('#coupon_code').fill('NEXO10');
    await page.locator('[name="apply_coupon"]').click();
    await page.waitForFunction(()=>document.body.innerText.includes('Cupom aplicado') || !!document.querySelector('.cart-discount'));
    await page.waitForLoadState('networkidle');
    assert.ok(await page.locator('.cart-discount').count());
    pass('NEXO10 coupon applied to cart');
    await page.goto(BASE+'/finalizar-compra/',{waitUntil:'networkidle'});
    await page.locator('#place_order').click();
    await page.waitForSelector('.woocommerce-error');
    assert.ok(await page.locator('.woocommerce-error li').count()>0);
    pass('checkout rejects missing required fields');
    await page.reload({waitUntil:'networkidle'});
    for (const [id,value] of Object.entries({billing_first_name:'Pessoa',billing_last_name:'Teste',billing_address_1:'Rua Ficticia, 123',billing_city:'Porto Alegre',billing_postcode:'90010-000',billing_email:'teste@example.com'})) {
      await page.locator('#'+id).fill(value);
    }
    await page.locator('#billing_state').selectOption('RS');
    await page.waitForTimeout(1800);
    assert.match(await page.locator('#payment').innerText(),/sem cobrança/);
    assert.equal(await page.locator('input[name="payment_method"]').count(),1);
    await page.screenshot({path:'artifacts/checkout-desktop.png',fullPage:true});
    await page.setViewportSize({width:390,height:844});
    assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth),true,'checkout mobile overflow');
    await page.screenshot({path:'artifacts/checkout-mobile.png',fullPage:true});
    pass('checkout fits mobile viewport');
    await page.setViewportSize({width:1440,height:1000});
    await page.locator('#place_order').click();
    await page.waitForURL(/order-received/, {timeout:30000});
    assert.match(await page.locator('body').innerText(),/Nenhum valor foi cobrado/);
    await page.screenshot({path:'artifacts/order-desktop.png',fullPage:true});
    pass('complete demo checkout without real payment');
    assert.deepEqual(errors,[]);
    pass('no browser JavaScript errors');
    fs.writeFileSync('artifacts/browser-results.json', JSON.stringify({passed,errors,date:new Date().toISOString()},null,2));
  } catch(error) {
    await page.screenshot({path:'artifacts/failure.png',fullPage:true});
    console.error(error);
    process.exitCode=1;
  } finally { await browser.close(); }
})();
