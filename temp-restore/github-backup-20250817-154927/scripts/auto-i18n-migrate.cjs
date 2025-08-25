// scripts/auto-i18n-migrate.cjs
const fs = require('fs');
const path = require('path');
const glob = require('glob');

const FR_JSON = 'auto_i18n_fr.json';
const EN_JSON = 'auto_i18n_en.json';
const LOG = 'auto_i18n_migration.log';

let frDict = {};
let enDict = {};
let keyId = 1;
let logLines = [];

function extractTemplates(content) {
  const regex = /<template[^>]*>([\s\S]*?)<\/template>/gi;
  const matches = [];
  let match;
  while ((match = regex.exec(content))) {
    matches.push({ start: match.index, end: regex.lastIndex, content: match[1] });
  }
  return matches;
}

function replaceStaticText(template, filePath) {
  // Match text nodes not inside {{ ... }} or v-bind, and not empty/whitespace
  // This is a simple heuristic, not a full HTML parser
  return template.replace(/>([^<>{}\n]+)</g, (m, txt) => {
    const clean = txt.trim();
    if (
      !clean ||
      clean.startsWith('{{') ||
      clean.endsWith('}}') ||
      clean.match(/^\s*$/) ||
      clean.match(/^\s*[\d.,:;!?-]+\s*$/)
    ) {
      return m;
    }
    const key = `auto.key${keyId++}`;
    frDict[key] = clean;
    enDict[key] = clean;
    logLines.push(`${filePath}: "${clean}" -> ${key}`);
    return `>{{ $t('${key}') }}<`;
  });
}

function processVueFile(filePath) {
  const orig = fs.readFileSync(filePath, 'utf8');
  const templates = extractTemplates(orig);
  if (!templates.length) return;

  let newContent = orig;
  templates.forEach(({ content, start, end }) => {
    const replaced = replaceStaticText(content, filePath);
    newContent =
      newContent.slice(0, start) +
      `<template>${replaced}</template>` +
      newContent.slice(end);
  });

  // Backup original
  fs.writeFileSync(filePath + '.bak', orig, 'utf8');
  fs.writeFileSync(filePath, newContent, 'utf8');
}

function main() {
  frDict = {};
  enDict = {};
  keyId = 1;
  logLines = [];

  const vueFiles = glob.sync('resources/js/**/*.vue');
  vueFiles.forEach(processVueFile);

  fs.writeFileSync(FR_JSON, JSON.stringify(frDict, null, 2), 'utf8');
  fs.writeFileSync(EN_JSON, JSON.stringify(enDict, null, 2), 'utf8');
  fs.writeFileSync(LOG, logLines.join('\n'), 'utf8');
  console.log('Migration terminée. Voir', LOG, 'pour le détail.');
}

main(); 