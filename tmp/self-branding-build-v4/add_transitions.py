import re, zipfile
from pathlib import Path
src=Path(r'C:\Nadu_System\nadu_system\tmp\self-branding-build-v4\self-branding-draft.pptx')
dst=Path(r'C:\Nadu_System\nadu_system\tmp\self-branding-build-v4\self-branding-animated-draft.pptx')
trans='<p:transition spd="med" advClick="1"><p:fade/></p:transition>'
patched=0
with zipfile.ZipFile(src,'r') as zin, zipfile.ZipFile(dst,'w',zipfile.ZIP_DEFLATED) as zout:
    for info in zin.infolist():
        data=zin.read(info.filename)
        if re.fullmatch(r'ppt/slides/slide[1-4]\.xml',info.filename):
            xml=data.decode('utf-8')
            marker='</p:cSld>'
            if xml.count(marker)!=1: raise RuntimeError(f'Unexpected slide XML structure: {info.filename}')
            xml=xml.replace(marker,marker+trans,1)
            data=xml.encode('utf-8'); patched+=1
        zout.writestr(info,data)
print('slides with fade transition:',patched)
