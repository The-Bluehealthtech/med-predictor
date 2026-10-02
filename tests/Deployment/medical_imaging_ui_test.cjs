/* Run against the synthetic Blade capture produced by MedicalImagingWorkflowTest. */
const {chromium}=require('playwright');
const fs=require('node:fs');
const assert=require('node:assert/strict');
const path=require('node:path');
(async()=>{
 const root=path.resolve(__dirname,'../..');
 const html=fs.readFileSync(process.env.FIT_IMAGING_UI_HTML||'/tmp/fit-imaging-screen.html','utf8');
 const browser=await chromium.launch({headless:true,executablePath:process.env.FIT_TEST_CHROME||undefined,args:['--no-sandbox']});
 const page=await browser.newPage({viewport:{width:1440,height:1100}});const errors=[];
 page.on('pageerror',e=>errors.push(e.message));
 await page.route('http://localhost/**',async route=>{
  const url=new URL(route.request().url());
  if(url.pathname==='/css/medical-imaging.css') return route.fulfill({contentType:'text/css',body:fs.readFileSync(path.join(root,'public/css/medical-imaging.css'))});
  if(url.pathname==='/js/medical-imaging.js') return route.fulfill({contentType:'application/javascript',body:fs.readFileSync(path.join(root,'public/js/medical-imaging.js'))});
  if(url.pathname.endsWith('/frame')) return route.fulfill({contentType:'image/png',body:fs.readFileSync(process.env.FIT_IMAGING_UI_PNG||'/tmp/fit-imaging-pixel-fixture.png')});
  return route.fulfill({contentType:'text/html; charset=utf-8',body:'<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>body{margin:0;background:#f1f5f9;font-family:Arial}</style></head><body>'+html+'</body></html>'});
 });
 await page.goto('http://localhost/imaging-fixture');await page.locator('#fi-viewer-message').waitFor({state:'hidden'});
 assert.equal(await page.locator('#fi-bookmark').isEnabled(),true);
 await page.locator('#fi-bookmark').click();assert.equal(await page.locator('#fi-references li').count(),1);
 assert.match(await page.locator('#fi-save-state').textContent(),/non enregistrées/);
 await page.locator('#fi-measure').click();const box=await page.locator('#fi-canvas').boundingBox();
 await page.mouse.click(box.x+box.width*.2,box.y+box.height*.2);await page.mouse.click(box.x+box.width*.7,box.y+box.height*.7);
 assert.equal(await page.locator('#fi-references li').count(),2);assert.equal(JSON.parse(await page.locator('#fi-reference-data').inputValue()).length,2);
 await page.locator('#fi-population').selectOption('male');await page.locator('#fi-grade').selectOption('6');assert.match(await page.locator('#fi-grade-notice').textContent(),/ne prouve pas/);
 await page.screenshot({path:'/tmp/fit-imaging-desktop.png',fullPage:true});
 assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true);
 await page.setViewportSize({width:390,height:844});await page.screenshot({path:'/tmp/fit-imaging-mobile.png',fullPage:true});
 assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true);
 await page.locator('#fi-population').selectOption('female');assert.equal(await page.locator('#fi-grade').isDisabled(),true);assert.equal(await page.locator('#fi-grade').inputValue(),'');
 assert.deepEqual(errors,[]);await browser.close();console.log('Image loading, references, measurements, U-17 limits and desktop/mobile layout: OK');
})().catch(e=>{console.error(e);process.exit(1)});
