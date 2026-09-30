const test=require('node:test');
const assert=require('node:assert/strict');
const fs=require('node:fs/promises');
const os=require('node:os');
const path=require('node:path');
const crypto=require('node:crypto');
const Service=require('../services/medGeminiService');
const {normalizeMedicalResult:normalize}=require('../utils/medicalResult');
test('zero is preserved; simulated and malformed results are rejected',()=>{
 assert.equal(normalize({success:true,text:'{"score":0}'}).score,0);
 for(const r of [{success:true,mockMode:true,text:'{}'},{success:true,note:'fallback',text:'{}'},
  {success:false,text:'{}'},{success:true,text:'not JSON'}]) assert.throws(()=>normalize(r));
});
test('unconfigured service never returns demo observations',async()=>{
 const s=new Service();s.mockMode=true;
 const r=await s.analyze('Fixture');assert.equal(r.success,false);assert.equal(r.text,undefined);
});
test('quota error never produces a clinical fallback',async()=>{
 const s=new Service();s.mockMode=false;s.model={generateContent:async()=>{throw Error('429 quota');}};
 const r=await s.analyze('Fixture');assert.equal(r.success,false);assert.equal(r.text,undefined);
});
test('transcription sends actual audio bytes without invented confidence',async()=>{
 const dir=await fs.mkdtemp(path.join(os.tmpdir(),'pcma-test-'));
 const file=path.join(dir,'fixture.wav');await fs.writeFile(file,'fixture audio bytes');
 const oldKey=process.env.OPENAI_API_KEY,oldFetch=global.fetch;
 process.env.OPENAI_API_KEY=crypto.randomBytes(16).toString('hex');
 let sent=false;
 global.fetch=async(url,o)=>{
  assert.ok(url.endsWith('/audio/transcriptions'));
  assert.equal(await o.body.get('file').text(),'fixture audio bytes');
  assert.equal(o.body.get('model'),'whisper-1');sent=true;
  return {ok:true,json:async()=>({text:'Fixture transcription'})};
 };
 try{
  const r=await new Service().transcribeAudio({audioFilePath:file});
  assert.equal(sent,true);assert.equal(r.transcription,'Fixture transcription');assert.equal(r.confidence,null);
 }finally{
  global.fetch=oldFetch;
  if(oldKey===undefined)delete process.env.OPENAI_API_KEY;else process.env.OPENAI_API_KEY=oldKey;
  await fs.rm(dir,{recursive:true,force:true});
 }
});
test('OCR reads the document bytes and rejects a mock response',async()=>{
 const dir=await fs.mkdtemp(path.join(os.tmpdir(),'pcma-test-'));
 const file=path.join(dir,'fixture.pdf');await fs.writeFile(file,'fixture PDF bytes');
 const s=new Service();
 s.analyzeMedicalImage=async(p,b,m)=>{
  assert.equal(b.toString(),'fixture PDF bytes');assert.equal(m,'application/pdf');
  return {success:true,text:'{"extracted_text":"Fixture document"}'};
 };
 try{
  const r=await s.extractTextFromImage({imageFilePath:file});
  assert.equal(r.extracted_text,'Fixture document');assert.equal(r.confidence,null);
  s.analyzeMedicalImage=async()=>({success:true,mockMode:true,text:'{}'});
  assert.equal((await s.extractTextFromImage({imageFilePath:file})).success,false);
 }finally{await fs.rm(dir,{recursive:true,force:true});}
});
test('service authentication requires a configured matching token',()=>{
 const auth=require('../middleware/auth'),old=process.env.AI_API_KEY;
 const token=crypto.randomBytes(16).toString('hex');process.env.AI_API_KEY=token;
 const res={status(n){this.code=n;return this;},json(v){this.data=v;return this;}};let passed=false;
 auth({headers:{}},res,()=>{passed=true;});assert.equal(res.code,401);assert.equal(passed,false);
 auth({headers:{authorization:'Bearer '+token}},res,()=>{passed=true;});assert.equal(passed,true);
 delete process.env.AI_API_KEY;auth({headers:{}},res,()=>{});assert.equal(res.code,503);
 if(old!==undefined)process.env.AI_API_KEY=old;
});
