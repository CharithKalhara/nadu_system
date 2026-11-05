import {FileBlob,PresentationFile} from '@oai/artifact-tool';
const p=await PresentationFile.importPptx(await FileBlob.load('C:/Nadu_System/nadu_system/output/presentations/Charith-Kalhara-Self-Branding-4-Slides.pptx'));
console.log('slides',p.slides.items.length,'noteWordCounts',p.slides.items.map(s=>String(s.speakerNotes.text).split(/\s+/).length).join(','));
