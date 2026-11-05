import {FileBlob,PresentationFile} from '@oai/artifact-tool';
const p=await PresentationFile.importPptx(await FileBlob.load('C:/Nadu_System/nadu_system/output/presentations/Charith-Kalhara-Self-Branding-Professional.pptx'));
for(const [i,s] of p.slides.items.entries()){const t=s.speakerNotes.text;console.log(i+1,typeof t==='string'?t.split(/\s+/).length:Array.isArray(t)?t.join(' ').split(/\s+/).length:JSON.stringify(t).split(/\s+/).length)}
