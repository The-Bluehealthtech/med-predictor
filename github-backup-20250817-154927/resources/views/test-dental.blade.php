<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dental Chart</title>
    <style>
        /* Dental Chart Styles */
        .dental-chart-container {
            position: relative;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .chart-wrapper {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            overflow-x: auto;
        }

        .dental-svg-container {
            position: relative;
            min-width: 800px;
        }

        .dental-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 1000;
            display: none;
        }

        .tooltip-content {
            line-height: 1.4;
        }

        /* SVG Styles */
        .tooth {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tooth:hover {
            opacity: 0.8;
        }

        .tooth-surface {
            cursor: pointer;
            transition: fill 0.2s ease;
        }

        .status-healthy {
            fill: #ffffff;
            stroke: #cccccc;
        }

        .status-caries {
            fill: #ff4d4d;
            stroke: #cc0000;
        }

        .status-restoration {
            fill: #4d94ff;
            stroke: #0066cc;
        }

        .status-crown {
            stroke: #4d94ff;
            stroke-width: 3px;
            fill-opacity: 0.1;
        }

        .status-missing {
            fill: #808080;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <h1>Test Dental Chart</h1>
        
        <!-- Interactive Dental Chart -->
        <div class="mt-4 bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <span class="mr-2">📊</span>
                Schéma Dentaire Interactif
            </h4>
            
            <div class="dental-chart-wrapper">
                <div class="dental-chart-container">
                    <div class="chart-header">
                        <div class="flex items-center space-x-4 text-sm mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-white border border-gray-300 rounded"></div>
                                <span>Santé</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-red-500 rounded"></div>
                                <span>Carie</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                <span>Restauration</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-gray-500 opacity-50 rounded"></div>
                                <span>Manquante</span>
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper" id="chartContainer">
                        <div 
                            class="dental-svg-container" 
                            id="dental-svg-container"
                        >
                            <!-- Dental Chart SVG will be injected here -->
                        </div>
                    </div>

                    <!-- Dental Tooltip -->
                    <div 
                        id="dental-tooltip"
                        class="dental-tooltip"
                        style="display: none;"
                    >
                        <div class="tooltip-content">
                            <div class="font-semibold" id="tooltip-tooth-number">Dent 1</div>
                            <div class="text-sm" id="tooltip-status">Saine</div>
                            <div class="text-xs text-gray-500" id="tooltip-condition">Aucune condition</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dental Status Summary -->
            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <h5 class="text-sm font-semibold text-gray-700 mb-3">Résumé Dentaire</h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Dents saines:</span>
                        <span class="font-semibold text-green-600" id="healthy-teeth-count">32</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Caries:</span>
                        <span class="font-semibold text-red-600" id="caries-count">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Restaurations:</span>
                        <span class="font-semibold text-blue-600" id="restoration-count">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Manquantes:</span>
                        <span class="font-semibold text-gray-600" id="missing-teeth-count">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dental Chart Functionality
        let teethStatus = {};
        let dentalTooltip = {
            visible: false,
            x: 0,
            y: 0,
            toothNumber: '',
            status: '',
            condition: ''
        };

        // Initialize dental chart
        function initializeDentalChart() {
            console.log('Initializing dental chart...');
            // Initialize teeth status for all 32 teeth
            for (let i = 1; i <= 32; i++) {
                teethStatus[i] = {
                    status: 'healthy',
                    condition: 'Normal',
                    surfaces: {
                        mesial: { condition: 'healthy', status: 'healthy' },
                        distal: { condition: 'healthy', status: 'healthy' },
                        occlusal: { condition: 'healthy', status: 'healthy' },
                        lingual: { condition: 'healthy', status: 'healthy' },
                        buccal: { condition: 'healthy', status: 'healthy' }
                    },
                    notes: ''
                };
            }
            
            // Load dental chart SVG
            loadDentalChart();
            updateDentalSummary();
        }

        // Load dental chart SVG
        function loadDentalChart() {
            console.log('Looking for dental chart container...');
            const chartContainer = document.getElementById('dental-svg-container');
            console.log('Chart container found:', !!chartContainer);
            
            if (!chartContainer) {
                console.error('Dental chart container not found');
                console.log('Available elements with dental in ID:', document.querySelectorAll('[id*="dental"]').length);
                return;
            }

            console.log('Loading dental chart SVG...');
            // Generate dental chart SVG
            const dentalChartSvg = generateDentalChartSvg();
            chartContainer.innerHTML = dentalChartSvg;
            console.log('Dental chart SVG loaded');
        }

        // Generate dental chart SVG
        function generateDentalChartSvg() {
            return `
                <svg width="800" height="400" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <style>
                            .tooth { cursor: pointer; transition: fill 0.2s ease; }
                            .tooth-surface { cursor: pointer; transition: fill 0.2s ease; }
                            .status-healthy { fill: #ffffff; stroke: #cccccc; }
                            .status-caries { fill: #ff4d4d; }
                            .status-restoration { fill: #4d94ff; }
                            .status-crown { stroke: #4d94ff; stroke-width: 3px; fill-opacity: 0.1; }
                            .status-missing { fill: #808080; opacity: 0.5; }
                        </style>
                    </defs>
                    
                    <!-- Upper teeth (1-16) -->
                    <g id="upper-teeth">
                        ${generateTeethRow(1, 16, 50, 100, 'upper')}
                    </g>
                    
                    <!-- Lower teeth (17-32) -->
                    <g id="lower-teeth">
                        ${generateTeethRow(17, 32, 50, 300, 'lower')}
                    </g>
                    
                    <!-- Labels -->
                    <text x="400" y="30" text-anchor="middle" font-size="16" font-weight="bold">Dentition Supérieure</text>
                    <text x="400" y="370" text-anchor="middle" font-size="16" font-weight="bold">Dentition Inférieure</text>
                </svg>
            `;
        }

        // Generate teeth row
        function generateTeethRow(start, end, y, baseY, position) {
            let svg = '';
            const teeth = end - start + 1;
            const spacing = 700 / teeth;
            
            for (let i = 0; i < teeth; i++) {
                const toothNumber = start + i;
                const x = 50 + (i * spacing);
                
                // Main tooth
                svg += `
                    <g id="tooth-${toothNumber}" class="tooth">
                        <rect 
                            id="tooth-${toothNumber}-main"
                            x="${x}" y="${baseY}" 
                            width="40" height="60" 
                            rx="5" 
                            class="tooth-surface status-healthy"
                            data-tooth="${toothNumber}"
                        />
                        <text 
                            x="${x + 20}" y="${baseY + 35}" 
                            text-anchor="middle" 
                            font-size="12" 
                            font-weight="bold"
                        >${toothNumber}</text>
                    </g>
                `;
            }
            
            return svg;
        }

        // Add dental chart event listeners
        function addDentalChartEventListeners() {
            console.log('Looking for dental chart container for event listeners...');
            const chartContainer = document.getElementById('dental-svg-container');
            console.log('Chart container found for event listeners:', !!chartContainer);
            
            if (!chartContainer) {
                console.error('Dental chart container not found for event listeners');
                console.log('Available elements with dental in ID:', document.querySelectorAll('[id*="dental"]').length);
                return;
            }

            console.log('Adding dental chart event listeners...');

            // Add click event listener for tooth status cycling
            chartContainer.addEventListener('click', function(event) {
                const target = event.target;
                const toothId = target.getAttribute('data-tooth');
                
                if (toothId) {
                    const toothNumber = parseInt(toothId);
                    console.log('Tooth clicked:', toothNumber);
                    cycleToothStatus(toothNumber);
                    updateDentalChartStyles();
                    updateDentalSummary();
                }
            });

            // Add mouseover event listener for tooltips
            chartContainer.addEventListener('mouseover', function(event) {
                const target = event.target;
                const toothId = target.getAttribute('data-tooth');
                
                if (toothId) {
                    const toothNumber = parseInt(toothId);
                    const tooth = teethStatus[toothNumber];
                    
                    dentalTooltip = {
                        visible: true,
                        x: event.clientX + 10,
                        y: event.clientY - 10,
                        toothNumber: toothNumber,
                        status: tooth.status,
                        condition: tooth.condition
                    };
                    
                    showDentalTooltip();
                }
            });

            // Add mouseleave event listener to hide tooltip
            chartContainer.addEventListener('mouseleave', function() {
                hideDentalTooltip();
            });

            console.log('Dental chart event listeners added');
        }

        // Show dental tooltip
        function showDentalTooltip() {
            const tooltip = document.getElementById('dental-tooltip');
            if (tooltip && dentalTooltip.visible) {
                tooltip.style.left = dentalTooltip.x + 'px';
                tooltip.style.top = dentalTooltip.y + 'px';
                tooltip.style.display = 'block';
                
                const toothNumberElement = document.getElementById('tooltip-tooth-number');
                const statusElement = document.getElementById('tooltip-status');
                const conditionElement = document.getElementById('tooltip-condition');
                
                if (toothNumberElement) toothNumberElement.textContent = `Dent ${dentalTooltip.toothNumber}`;
                if (statusElement) statusElement.textContent = dentalTooltip.status;
                if (conditionElement) conditionElement.textContent = dentalTooltip.condition;
            }
        }

        // Hide dental tooltip
        function hideDentalTooltip() {
            dentalTooltip.visible = false;
            hideDentalTooltipElement();
        }

        // Hide dental tooltip element
        function hideDentalTooltipElement() {
            const tooltip = document.getElementById('dental-tooltip');
            if (tooltip) {
                tooltip.style.display = 'none';
            }
        }

        // Cycle tooth status
        function cycleToothStatus(toothNumber) {
            const statuses = ['healthy', 'caries', 'restoration', 'missing'];
            const currentStatus = teethStatus[toothNumber].status;
            const currentIndex = statuses.indexOf(currentStatus);
            const nextIndex = (currentIndex + 1) % statuses.length;
            const newStatus = statuses[nextIndex];
            
            teethStatus[toothNumber].status = newStatus;
            teethStatus[toothNumber].condition = getDentalConditionLabel(newStatus);
            console.log(`Tooth ${toothNumber} status changed to: ${newStatus}`);
        }

        // Get dental condition label
        function getDentalConditionLabel(status) {
            const labels = {
                'healthy': 'Normal',
                'caries': 'Carie',
                'restoration': 'Restauration',
                'missing': 'Manquante'
            };
            return labels[status] || 'Normal';
        }

        // Update dental chart styles
        function updateDentalChartStyles() {
            Object.keys(teethStatus).forEach(toothNumber => {
                const tooth = teethStatus[toothNumber];
                const mainElement = document.getElementById(`tooth-${toothNumber}-main`);
                
                if (mainElement) {
                    mainElement.className = `tooth-surface status-${tooth.status}`;
                }
            });
        }

        // Update dental summary
        function updateDentalSummary() {
            const healthyCount = Object.values(teethStatus).filter(tooth => tooth.status === 'healthy').length;
            const cariesCount = Object.values(teethStatus).filter(tooth => tooth.status === 'caries').length;
            const restorationCount = Object.values(teethStatus).filter(tooth => tooth.status === 'restoration').length;
            const missingCount = Object.values(teethStatus).filter(tooth => tooth.status === 'missing').length;

            const healthyElement = document.getElementById('healthy-teeth-count');
            const cariesElement = document.getElementById('caries-count');
            const restorationElement = document.getElementById('restoration-count');
            const missingElement = document.getElementById('missing-teeth-count');

            if (healthyElement) healthyElement.textContent = healthyCount;
            if (cariesElement) cariesElement.textContent = cariesCount;
            if (restorationElement) restorationElement.textContent = restorationCount;
            if (missingElement) missingElement.textContent = missingCount;
        }

        // Initialize dental chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing dental chart...');
            setTimeout(function() {
                console.log('Initializing dental chart with delay...');
                initializeDentalChart();
                addDentalChartEventListeners();
                console.log('Dental chart initialization complete');
            }, 100);
        });
    </script>
</body>
</html> 