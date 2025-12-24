# PWA Setup Guide

## Installing as Desktop App

### Chrome/Edge (Windows/Mac/Linux)
1. Open the application in Chrome or Edge
2. Look for the install icon in the address bar (or menu → Install)
3. Click "Install" to add to your desktop/home screen
4. The app will launch in standalone mode

### Safari (Mac/iOS)
1. Open the application in Safari
2. Tap the Share button
3. Select "Add to Home Screen"
4. The app will appear as an icon on your home screen

### Firefox
1. Open the application in Firefox
2. Look for the install prompt or go to menu → Install
3. Follow the installation prompts

## Mobile Access

### Super Admin Mobile Dashboard
- Navigate to `/mobile` route (Super Admin only)
- Optimized for mobile viewing
- Shows critical financial insights
- System health monitoring
- Term-based summaries

## Service Worker

The service worker provides:
- Basic offline caching
- Faster page loads
- Improved performance

## Icons

The manifest references icon files that should be placed in the `public` directory:
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png

**Note**: You can generate these icons from a single 512x512 PNG image using online PWA icon generators.

## Testing PWA

1. Open Chrome DevTools (F12)
2. Go to "Application" tab
3. Check "Manifest" section
4. Test "Service Workers" section
5. Use "Lighthouse" to audit PWA features

