import fs from 'node:fs';
import {createRequire} from 'node:module';
const root=process.env.FIT_TEST_NODE_ROOT||new URL('../..',import.meta.url).pathname;
const require=createRequire(root+'/package.json');
const {parse,compile}=require('@vue/compiler-dom');
const Vue=require('vue');
const {renderToString}=require('@vue/server-renderer');
const html=fs.readFileSync(process.argv[2]||'/tmp/fit-health-create-fixture.html','utf8');
const ast=parse(html);
function find(n,id){
    if(n.props?.some(p=>p.name==='id'&&p.value?.content===id))return n;
    for(const c of n.children||[]){const r=find(c,id);if(r)return r;}
}
const tabs=find(ast,'health-records-tabs');
if(!tabs)throw new Error('Racine des onglets absente');
new Function(compile(html.slice(tabs.loc.start.offset,tabs.loc.end.offset)).code);
const aut=find(ast,'embedded-aut');
if(!aut)throw new Error('Formulaire AUT absent');
const render=new Function('Vue',compile(html.slice(aut.loc.start.offset,aut.loc.end.offset)).code)(Vue);
render._rc=true; // Même marqueur que le compilateur Vue exécuté par le navigateur.
for(const enabled of [false,true]){
    const warnings=[];
    const app=Vue.createSSRApp({render,data:()=>({autEnabled:enabled})});
    app.config.warnHandler=message=>warnings.push(message);
    const result=await renderToString(app);
    if(warnings.length)throw new Error(warnings.join('; '));
    const disabled=/<fieldset[^>]*\sdisabled(?:[\s=>])/.test(result);
    if(disabled===enabled)throw new Error('Activation du formulaire incorrecte');
    if(!result.includes('aut_form[substance_1]'))throw new Error('Champ substance absent');
}
console.log('Template Vue valide ; formulaire AUT désactivé puis activé correctement.');
