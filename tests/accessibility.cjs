const { chromium } = require('playwright');
const { default: AxeBuilder } = require('@axe-core/playwright');
const fs = require('node:fs');
(async()=>{
 const browser=await chromium.launch();
 const context=await browser.newContext();
 const page=await context.newPage();
 const reports=[];
 for (const width of [390,1440]) {
  await page.setViewportSize({width,height:900});
  for(const route of ['/','/loja/','/produto/headphone-studio-one/']) {
   await page.goto('http://localhost:8092'+route,{waitUntil:'networkidle'});
   const {violations}=await new AxeBuilder({page}).withTags(['wcag2a','wcag2aa','wcag21aa']).analyze();
   reports.push({width,route,violations:violations.map(v=>({id:v.id,impact:v.impact,description:v.description,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))}))});
   console.log(width,route,'violations:',violations.length);
  }
 }
 fs.writeFileSync('artifacts/accessibility-results.json',JSON.stringify(reports,null,2));
 if(reports.some(r=>r.violations.length))process.exitCode=1;
 await browser.close();
})();
