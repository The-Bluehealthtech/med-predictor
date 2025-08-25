/**
 * Viewer d'Images Médicales Avancé
 * Supporte DICOM et formats standards (JPG, PNG, TIFF)
 */

class MedicalImageViewer {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.currentImage = null;
        this.zoomLevel = 1;
        this.panX = 0;
        this.panY = 0;
        this.isDragging = false;
        this.lastMousePos = { x: 0, y: 0 };
        this.measurements = [];
        this.annotations = [];
        this.dicomData = null;
        
        this.initViewer();
    }

    initViewer() {
        // Créer l'interface du viewer
        this.container.innerHTML = `
            <div class="medical-viewer-container">
                <!-- Toolbar -->
                <div class="viewer-toolbar">
                    <div class="toolbar-group">
                        <button class="tool-btn" id="zoom-in" title="Zoom +">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                        <button class="tool-btn" id="zoom-out" title="Zoom -">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <path d="M3 8h10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </button>
                        <button class="tool-btn" id="reset-view" title="Vue d'ensemble">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <path d="M2 2h12v12H2z" stroke="currentColor" fill="none"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="tool-btn" id="measure-tool" title="Mesure">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <path d="M2 2l12 12M2 14l12-12" stroke="currentColor"/>
                            </svg>
                        </button>
                        <button class="tool-btn" id="annotate-tool" title="Annotation">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" fill="none"/>
                            </svg>
                        </button>
                        <button class="tool-btn" id="window-level" title="Fenêtrage">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <rect x="2" y="2" width="12" height="12" stroke="currentColor" fill="none"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="toolbar-group">
                        <button class="tool-btn" id="dicom-info" title="Info DICOM">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <circle cx="8" cy="8" r="7" stroke="currentColor" fill="none"/>
                                <path d="M8 4v4M8 12h.01" stroke="currentColor"/>
                            </svg>
                        </button>
                        <button class="tool-btn" id="export-image" title="Exporter">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" fill="none"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Viewer Area -->
                <div class="viewer-area">
                    <div class="image-container" id="image-container">
                        <canvas id="viewer-canvas"></canvas>
                        <div class="measurement-overlay" id="measurement-overlay"></div>
                        <div class="annotation-overlay" id="annotation-overlay"></div>
                    </div>
                    
                    <!-- Info Panel -->
                    <div class="info-panel" id="info-panel">
                        <h4>Informations Image</h4>
                        <div id="image-info"></div>
                        <div id="dicom-info-panel" style="display: none;">
                            <h5>DICOM Tags</h5>
                            <div id="dicom-tags"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Measurement Panel -->
                <div class="measurement-panel" id="measurement-panel" style="display: none;">
                    <h4>Mesures</h4>
                    <div id="measurements-list"></div>
                    <button id="clear-measurements" class="btn-secondary">Effacer</button>
                </div>
            </div>
        `;

        this.setupEventListeners();
        this.setupStyles();
    }

    setupStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .medical-viewer-container {
                display: flex;
                flex-direction: column;
                height: 600px;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background: #f8fafc;
            }
            
            .viewer-toolbar {
                display: flex;
                justify-content: space-between;
                padding: 8px;
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
                border-radius: 8px 8px 0 0;
            }
            
            .toolbar-group {
                display: flex;
                gap: 4px;
            }
            
            .tool-btn {
                padding: 6px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                background: #ffffff;
                cursor: pointer;
                color: #374151;
                transition: all 0.2s;
            }
            
            .tool-btn:hover {
                background: #f3f4f6;
                border-color: #9ca3af;
            }
            
            .tool-btn.active {
                background: #3b82f6;
                color: #ffffff;
                border-color: #2563eb;
            }
            
            .viewer-area {
                display: flex;
                flex: 1;
                overflow: hidden;
            }
            
            .image-container {
                flex: 1;
                position: relative;
                overflow: hidden;
                background: #000000;
                cursor: crosshair;
            }
            
            #viewer-canvas {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                max-width: 100%;
                max-height: 100%;
            }
            
            .measurement-overlay,
            .annotation-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
            }
            
            .info-panel {
                width: 300px;
                background: #ffffff;
                border-left: 1px solid #e2e8f0;
                padding: 16px;
                overflow-y: auto;
            }
            
            .measurement-panel {
                width: 250px;
                background: #ffffff;
                border-left: 1px solid #e2e8f0;
                padding: 16px;
                overflow-y: auto;
            }
            
            .measurement-line {
                position: absolute;
                background: #ff0000;
                height: 2px;
                pointer-events: none;
            }
            
            .measurement-label {
                position: absolute;
                background: rgba(255, 0, 0, 0.8);
                color: #ffffff;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 12px;
                pointer-events: none;
            }
            
            .annotation-marker {
                position: absolute;
                width: 12px;
                height: 12px;
                background: #ffff00;
                border: 2px solid #000000;
                border-radius: 50%;
                cursor: pointer;
                pointer-events: auto;
            }
            
            .annotation-popup {
                position: absolute;
                background: #ffffff;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                display: none;
            }
            
            .window-level-controls {
                position: absolute;
                top: 10px;
                right: 10px;
                background: rgba(0, 0, 0, 0.7);
                color: #ffffff;
                padding: 8px;
                border-radius: 4px;
                display: none;
            }
            
            .window-level-controls input {
                width: 100px;
                margin: 4px 0;
            }
        `;
        document.head.appendChild(style);
    }

    setupEventListeners() {
        // Toolbar buttons
        document.getElementById('zoom-in').addEventListener('click', () => this.zoom(1.2));
        document.getElementById('zoom-out').addEventListener('click', () => this.zoom(0.8));
        document.getElementById('reset-view').addEventListener('click', () => this.resetView());
        document.getElementById('measure-tool').addEventListener('click', () => this.toggleMeasureTool());
        document.getElementById('annotate-tool').addEventListener('click', () => this.toggleAnnotateTool());
        document.getElementById('window-level').addEventListener('click', () => this.toggleWindowLevel());
        document.getElementById('dicom-info').addEventListener('click', () => this.showDicomInfo());
        document.getElementById('export-image').addEventListener('click', () => this.exportImage());

        // Mouse events
        const imageContainer = document.getElementById('image-container');
        imageContainer.addEventListener('mousedown', (e) => this.onMouseDown(e));
        imageContainer.addEventListener('mousemove', (e) => this.onMouseMove(e));
        imageContainer.addEventListener('mouseup', (e) => this.onMouseUp(e));
        imageContainer.addEventListener('wheel', (e) => this.onWheel(e));

        // Measurement panel
        document.getElementById('clear-measurements').addEventListener('click', () => this.clearMeasurements());
    }

    loadImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                const data = e.target.result;
                
                // Vérifier si c'est un fichier DICOM
                if (this.isDicomFile(data)) {
                    this.loadDicomImage(data);
                } else {
                    this.loadStandardImage(data);
                }
                
                resolve();
            };
            
            reader.onerror = reject;
            reader.readAsArrayBuffer(file);
        });
    }

    isDicomFile(data) {
        // Vérifier la signature DICOM
        const view = new DataView(data);
        const signature = String.fromCharCode(
            view.getUint8(128),
            view.getUint8(129),
            view.getUint8(130),
            view.getUint8(131)
        );
        return signature === 'DICM';
    }

    loadDicomImage(data) {
        // Simulation du chargement DICOM
        // En production, utilisez une bibliothèque comme cornerstone.js
        console.log('Chargement DICOM...');
        
        // Créer une image de test pour la démo
        this.createTestImage();
        this.dicomData = this.parseDicomData(data);
        this.showDicomInfo();
    }

    loadStandardImage(data) {
        const img = new Image();
        img.onload = () => {
            this.currentImage = img;
            this.renderImage();
            this.updateImageInfo();
        };
        img.src = URL.createObjectURL(new Blob([data]));
    }

    createTestImage() {
        // Créer une image de test pour la démo
        const canvas = document.createElement('canvas');
        canvas.width = 512;
        canvas.height = 512;
        const ctx = canvas.getContext('2d');
        
        // Créer un motif de test
        ctx.fillStyle = '#f0f0f0';
        ctx.fillRect(0, 0, 512, 512);
        
        // Ajouter des lignes de grille
        ctx.strokeStyle = '#cccccc';
        ctx.lineWidth = 1;
        for (let i = 0; i < 512; i += 32) {
            ctx.beginPath();
            ctx.moveTo(i, 0);
            ctx.lineTo(i, 512);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(0, i);
            ctx.lineTo(512, i);
            ctx.stroke();
        }
        
        // Ajouter du texte
        ctx.fillStyle = '#000000';
        ctx.font = '16px Arial';
        ctx.fillText('Image DICOM de Test', 20, 30);
        ctx.fillText('512x512 pixels', 20, 50);
        
        this.currentImage = canvas;
        this.renderImage();
    }

    parseDicomData(data) {
        // Simulation du parsing DICOM
        return {
            'Patient Name': 'Test Patient',
            'Patient ID': '12345',
            'Study Date': '2024-01-15',
            'Modality': 'CT',
            'Image Size': '512x512',
            'Window Center': 40,
            'Window Width': 400
        };
    }

    renderImage() {
        if (!this.currentImage) return;
        
        const canvas = document.getElementById('viewer-canvas');
        const ctx = canvas.getContext('2d');
        
        // Ajuster la taille du canvas
        const container = document.getElementById('image-container');
        const containerRect = container.getBoundingClientRect();
        
        canvas.width = containerRect.width;
        canvas.height = containerRect.height;
        
        // Calculer les dimensions de l'image
        const imgWidth = this.currentImage.width;
        const imgHeight = this.currentImage.height;
        
        // Appliquer zoom et pan
        const scaledWidth = imgWidth * this.zoomLevel;
        const scaledHeight = imgHeight * this.zoomLevel;
        
        // Centrer l'image
        const x = (canvas.width - scaledWidth) / 2 + this.panX;
        const y = (canvas.height - scaledHeight) / 2 + this.panY;
        
        // Dessiner l'image
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(this.currentImage, x, y, scaledWidth, scaledHeight);
        
        // Redessiner les mesures et annotations
        this.renderMeasurements();
        this.renderAnnotations();
    }

    zoom(factor) {
        this.zoomLevel *= factor;
        this.zoomLevel = Math.max(0.1, Math.min(5, this.zoomLevel));
        this.renderImage();
    }

    resetView() {
        this.zoomLevel = 1;
        this.panX = 0;
        this.panY = 0;
        this.renderImage();
    }

    onMouseDown(e) {
        this.isDragging = true;
        this.lastMousePos = { x: e.clientX, y: e.clientY };
        e.preventDefault();
    }

    onMouseMove(e) {
        if (!this.isDragging) return;
        
        const deltaX = e.clientX - this.lastMousePos.x;
        const deltaY = e.clientY - this.lastMousePos.y;
        
        this.panX += deltaX;
        this.panY += deltaY;
        
        this.lastMousePos = { x: e.clientX, y: e.clientY };
        this.renderImage();
    }

    onMouseUp(e) {
        this.isDragging = false;
    }

    onWheel(e) {
        e.preventDefault();
        const factor = e.deltaY > 0 ? 0.9 : 1.1;
        this.zoom(factor);
    }

    toggleMeasureTool() {
        const btn = document.getElementById('measure-tool');
        btn.classList.toggle('active');
        
        if (btn.classList.contains('active')) {
            this.startMeasurement();
        } else {
            this.stopMeasurement();
        }
    }

    startMeasurement() {
        const container = document.getElementById('image-container');
        container.style.cursor = 'crosshair';
        
        container.addEventListener('click', this.onMeasurementClick.bind(this));
    }

    stopMeasurement() {
        const container = document.getElementById('image-container');
        container.style.cursor = 'default';
        
        container.removeEventListener('click', this.onMeasurementClick.bind(this));
    }

    onMeasurementClick(e) {
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        this.addMeasurementPoint(x, y);
    }

    addMeasurementPoint(x, y) {
        this.measurements.push({ x, y, timestamp: Date.now() });
        
        if (this.measurements.length >= 2) {
            this.calculateMeasurement();
        }
        
        this.renderMeasurements();
        this.updateMeasurementPanel();
    }

    calculateMeasurement() {
        const p1 = this.measurements[this.measurements.length - 2];
        const p2 = this.measurements[this.measurements.length - 1];
        
        const distance = Math.sqrt(
            Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2)
        );
        
        // Convertir en mm (exemple)
        const mmPerPixel = 0.1; // À ajuster selon l'image
        const distanceMm = distance * mmPerPixel;
        
        p2.distance = distanceMm;
        p2.point1 = p1;
    }

    renderMeasurements() {
        const overlay = document.getElementById('measurement-overlay');
        overlay.innerHTML = '';
        
        for (let i = 0; i < this.measurements.length; i++) {
            const point = this.measurements[i];
            
            // Point de mesure
            const marker = document.createElement('div');
            marker.style.position = 'absolute';
            marker.style.left = point.x + 'px';
            marker.style.top = point.y + 'px';
            marker.style.width = '8px';
            marker.style.height = '8px';
            marker.style.backgroundColor = '#ff0000';
            marker.style.borderRadius = '50%';
            marker.style.transform = 'translate(-50%, -50%)';
            overlay.appendChild(marker);
            
            // Ligne de mesure
            if (i > 0 && point.distance) {
                const prevPoint = this.measurements[i - 1];
                const line = document.createElement('div');
                line.style.position = 'absolute';
                line.style.left = prevPoint.x + 'px';
                line.style.top = prevPoint.y + 'px';
                line.style.width = '2px';
                line.style.height = point.distance / 0.1 + 'px';
                line.style.backgroundColor = '#ff0000';
                line.style.transformOrigin = '0 0';
                
                const angle = Math.atan2(point.y - prevPoint.y, point.x - prevPoint.x);
                line.style.transform = `rotate(${angle}rad)`;
                
                overlay.appendChild(line);
                
                // Label de mesure
                const label = document.createElement('div');
                label.style.position = 'absolute';
                label.style.left = (prevPoint.x + point.x) / 2 + 'px';
                label.style.top = (prevPoint.y + point.y) / 2 + 'px';
                label.style.backgroundColor = 'rgba(255, 0, 0, 0.8)';
                label.style.color = '#ffffff';
                label.style.padding = '2px 6px';
                label.style.borderRadius = '3px';
                label.style.fontSize = '12px';
                label.style.transform = 'translate(-50%, -50%)';
                label.textContent = `${point.distance.toFixed(1)} mm`;
                overlay.appendChild(label);
            }
        }
    }

    updateMeasurementPanel() {
        const panel = document.getElementById('measurement-panel');
        const list = document.getElementById('measurements-list');
        
        panel.style.display = 'block';
        list.innerHTML = '';
        
        for (let i = 0; i < this.measurements.length; i++) {
            const point = this.measurements[i];
            const item = document.createElement('div');
            item.style.padding = '4px 0';
            item.style.borderBottom = '1px solid #e2e8f0';
            
            if (point.distance) {
                item.textContent = `Mesure ${Math.floor(i/2) + 1}: ${point.distance.toFixed(1)} mm`;
            } else {
                item.textContent = `Point ${i + 1}: (${point.x.toFixed(0)}, ${point.y.toFixed(0)})`;
            }
            
            list.appendChild(item);
        }
    }

    clearMeasurements() {
        this.measurements = [];
        document.getElementById('measurement-overlay').innerHTML = '';
        document.getElementById('measurement-panel').style.display = 'none';
    }

    toggleAnnotateTool() {
        const btn = document.getElementById('annotate-tool');
        btn.classList.toggle('active');
        
        if (btn.classList.contains('active')) {
            this.startAnnotation();
        } else {
            this.stopAnnotation();
        }
    }

    startAnnotation() {
        const container = document.getElementById('image-container');
        container.addEventListener('click', this.onAnnotationClick.bind(this));
    }

    stopAnnotation() {
        const container = document.getElementById('image-container');
        container.removeEventListener('click', this.onAnnotationClick.bind(this));
    }

    onAnnotationClick(e) {
        const rect = e.target.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        this.addAnnotation(x, y);
    }

    addAnnotation(x, y) {
        const annotation = {
            id: Date.now(),
            x, y,
            text: prompt('Texte de l\'annotation:') || 'Annotation'
        };
        
        this.annotations.push(annotation);
        this.renderAnnotations();
    }

    renderAnnotations() {
        const overlay = document.getElementById('annotation-overlay');
        overlay.innerHTML = '';
        
        this.annotations.forEach(annotation => {
            const marker = document.createElement('div');
            marker.className = 'annotation-marker';
            marker.style.left = annotation.x + 'px';
            marker.style.top = annotation.y + 'px';
            marker.style.transform = 'translate(-50%, -50%)';
            
            marker.addEventListener('click', () => this.showAnnotationPopup(annotation, marker));
            
            overlay.appendChild(marker);
        });
    }

    showAnnotationPopup(annotation, marker) {
        const popup = document.createElement('div');
        popup.className = 'annotation-popup';
        popup.style.left = annotation.x + 20 + 'px';
        popup.style.top = annotation.y - 20 + 'px';
        popup.innerHTML = `
            <div><strong>Annotation:</strong></div>
            <div>${annotation.text}</div>
            <button onclick="this.parentElement.remove()" style="margin-top: 8px;">Fermer</button>
        `;
        
        document.getElementById('annotation-overlay').appendChild(popup);
        
        // Fermer après 5 secondes
        setTimeout(() => {
            if (popup.parentElement) {
                popup.remove();
            }
        }, 5000);
    }

    toggleWindowLevel() {
        const btn = document.getElementById('window-level');
        btn.classList.toggle('active');
        
        if (btn.classList.contains('active')) {
            this.showWindowLevelControls();
        } else {
            this.hideWindowLevelControls();
        }
    }

    showWindowLevelControls() {
        const controls = document.createElement('div');
        controls.className = 'window-level-controls';
        controls.innerHTML = `
            <div>Fenêtrage DICOM</div>
            <div>
                <label>Centre: <input type="range" id="window-center" min="0" max="255" value="40"></label>
            </div>
            <div>
                <label>Largeur: <input type="range" id="window-width" min="1" max="255" value="400"></label>
            </div>
        `;
        
        document.getElementById('image-container').appendChild(controls);
        
        // Event listeners pour les contrôles
        document.getElementById('window-center').addEventListener('input', (e) => {
            this.updateWindowLevel(parseInt(e.target.value), null);
        });
        
        document.getElementById('window-width').addEventListener('input', (e) => {
            this.updateWindowLevel(null, parseInt(e.target.value));
        });
    }

    hideWindowLevelControls() {
        const controls = document.querySelector('.window-level-controls');
        if (controls) {
            controls.remove();
        }
    }

    updateWindowLevel(center, width) {
        // Simulation de l'ajustement du fenêtrage DICOM
        console.log('Mise à jour fenêtrage:', { center, width });
        this.renderImage();
    }

    showDicomInfo() {
        if (!this.dicomData) return;
        
        const panel = document.getElementById('dicom-info-panel');
        const tags = document.getElementById('dicom-tags');
        
        panel.style.display = 'block';
        tags.innerHTML = '';
        
        Object.entries(this.dicomData).forEach(([key, value]) => {
            const item = document.createElement('div');
            item.style.padding = '4px 0';
            item.style.borderBottom = '1px solid #e2e8f0';
            item.innerHTML = `<strong>${key}:</strong> ${value}`;
            tags.appendChild(item);
        });
    }

    updateImageInfo() {
        const info = document.getElementById('image-info');
        if (this.currentImage) {
            info.innerHTML = `
                <div><strong>Dimensions:</strong> ${this.currentImage.width} x ${this.currentImage.height}</div>
                <div><strong>Zoom:</strong> ${(this.zoomLevel * 100).toFixed(0)}%</div>
                <div><strong>Format:</strong> ${this.dicomData ? 'DICOM' : 'Standard'}</div>
            `;
        }
    }

    exportImage() {
        const canvas = document.getElementById('viewer-canvas');
        const link = document.createElement('a');
        link.download = 'medical-image.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}

// Initialisation globale
window.MedicalImageViewer = MedicalImageViewer; 