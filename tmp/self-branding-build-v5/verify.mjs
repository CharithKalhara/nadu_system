import {FileBlob,PresentationFile} from '@oai/artifact-tool';
const p=await PresentationFile.importPptx(await FileBlob.load('C:/Nadu_System/nadu_system/output/presentations/Charith-Kalhara-Self-Branding-Focused.pptx'));
const slides=p.slides.items;
const text=slides.map(s=>[...s.shapes.items.map(x=>x.text?.toString?.()??''),String(s.speakerNotes.text??'')].join(' ')).join(' ');
console.log('slides',slides.length,'has project term',/projects?/i.test(text),'notes',slides.map(s=>String(s.speakerNotes.text).split(/\s+/).length).join(','));
