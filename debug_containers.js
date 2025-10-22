// Script de debug pour identifier les conteneurs disponibles
console.log('🔍 Debug: Available containers on page');

// Chercher tous les conteneurs possibles
const selectors = [
    '.curriculum-content',
    '.course-content-area', 
    '.main-content',
    '.video-content',
    '.course-learn-content',
    '#main-content',
    '.content-area',
    'main',
    '.container',
    '.content',
    'div[class*="content"]',
    '.plyr',
    'video',
    'iframe'
];

console.log('📋 Checking selectors:');
selectors.forEach(selector => {
    const elements = document.querySelectorAll(selector);
    if (elements.length > 0) {
        console.log(`✅ Found ${elements.length} element(s) for "${selector}":`, elements);
    } else {
        console.log(`❌ No elements found for "${selector}"`);
    }
});

// Chercher tous les divs avec des classes contenant "content"
const contentDivs = document.querySelectorAll('div[class*="content"]');
console.log('📦 All divs with "content" in class:', contentDivs);

// Chercher la structure de la page
const main = document.querySelector('main');
if (main) {
    console.log('🏗️ Main structure:', main);
    console.log('🏗️ Main children:', main.children);
}

// Chercher les conteneurs de vidéo spécifiquement
const videoContainers = document.querySelectorAll('.plyr, video, iframe, [class*="video"], [class*="player"]');
console.log('🎥 Video containers:', videoContainers);

console.log('🔍 Debug complete');



