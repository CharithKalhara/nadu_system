import path from 'node:path';
import {pathToFileURL} from 'node:url';
const skillDir='C:/Users/chari/.codex/plugins/cache/openai-primary-runtime/presentations/26.1013.11914/skills/presentations';
const workspaceDir='C:/Nadu_System/nadu_system';
const {finalizePresentation}=await import(pathToFileURL(path.join(skillDir,'container_tools/artifact_tool_utils.mjs')).href);
const staging='C:/Nadu_System/nadu_system/tmp/self-branding-build-v4';
const finalPath='C:/Nadu_System/nadu_system/output/presentations/Charith-Kalhara-Self-Branding-Visual-Animated.pptx';
const result=await finalizePresentation({explicitTotalSlideCount:4,workspaceDir,candidatePath:path.join(staging,'self-branding-animated-draft.pptx'),finalPath,pythonExecutable:'C:/Users/chari/.cache/codex-runtimes/codex-primary-runtime/dependencies/python/python.exe',integrityValidatorPath:path.join(skillDir,'container_tools/inspect_presentation_package_integrity.py'),layoutValidatorPath:path.join(skillDir,'container_tools/inspect_presentation_layout_geometry.py'),layoutArgs:['--expected-slide-size-emu','12192000,6858000','--validate-bullet-geometry','--validate-heading-fit'],requiredNativeTableOwnerSlides:[],fontPolicy:{basis:'design',families:['Aptos']},verifyArtifactToolImport:true,receiptPath:path.join(staging,'Charith-Kalhara-Self-Branding-Visual-Animated.validation.json')});
console.log(JSON.stringify({finalPath:result.finalPath,packageIntegrity:result.packageIntegrity?.status,slides:result.presentationLayout?.slide_count,layoutWarnings:result.presentationLayout?.warning_count,importPassed:result.firstPartyImport?.passed},null,2));
