import re,zipfile
from pathlib import Path
src=Path(r'C:\Nadu_System\nadu_system\tmp\self-branding-build-v5\self-branding-draft.pptx')
dst=Path(r'C:\Nadu_System\nadu_system\tmp\self-branding-build-v5\self-branding-animated-draft.pptx')
transition='<p:transition spd="med" advClick="1"><p:fade/></p:transition>'
count=0
with zipfile.ZipFile(src,'r') as zin,zipfile.ZipFile(dst,'w',zipfile.ZIP_DEFLATED) as zout:
 for item in zin.infolist():
  data=zin.read(item.filename)
  if re.fullmatch(r'ppt/slides/slide[1-4]\.xml',item.filename):
   xml=data.decode('utf-8')
   if xml.count('</p:cSld>')!=1:raise RuntimeError(item.filename)
   data=xml.replace('</p:cSld>','</p:cSld>'+transition,1).encode('utf-8');count+=1
  zout.writestr(item,data)
print('Fade transitions added:',count)
