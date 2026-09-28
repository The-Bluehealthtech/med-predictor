#!/usr/bin/env python3
import csv
import sys
import zipfile
import xml.etree.ElementTree as ET

source = sys.argv[1]
target = sys.argv[2]
ns = {'m': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}

with zipfile.ZipFile(source) as archive:
    strings = [
        ''.join(node.itertext())
        for node in ET.fromstring(archive.read('xl/sharedStrings.xml')).findall('m:si', ns)
    ]
    sheet = ET.fromstring(archive.read('xl/worksheets/sheet1.xml'))
    rows = []
    for row in sheet.findall('.//m:sheetData/m:row', ns):
        values = []
        for cell in row.findall('m:c', ns):
            value = cell.find('m:v', ns)
            text = '' if value is None else value.text
            if cell.get('t') == 's' and text:
                text = strings[int(text)]
            values.append(text)
        rows.append(values)

with open(target, 'w', newline='', encoding='utf-8') as output:
    csv.writer(output).writerows(rows)
print(f'Export Excel converti: {len(rows) - 1} joueurs, {len(rows[0])} colonnes')
