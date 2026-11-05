import zipfile,re
p=r'C:\Nadu_System\nadu_system\output\presentations\Charith-Kalhara-Self-Branding-Visual-Animated.pptx'
z=zipfile.ZipFile(p)
slides=[z.read(n).decode() for n in z.namelist() if re.fullmatch(r'ppt/slides/slide[1-4]\.xml',n)]
print('slides:',len(slides),'fade transitions:',sum('<p:transition spd="med" advClick="1"><p:fade/></p:transition>' in x for x in slides))
