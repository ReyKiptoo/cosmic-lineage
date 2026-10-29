import Alpine from 'alpinejs';
import { historyApp } from './history.js';
import { inquiryCanvasApp } from './inquiryCanvas.js';

window.Alpine = Alpine;
window.historyApp = historyApp;
window.inquiryCanvasApp = inquiryCanvasApp;

Alpine.start();
