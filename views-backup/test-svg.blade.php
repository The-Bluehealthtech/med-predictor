<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SVG Postural Charts - Realistic Human Body Design</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .svg-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
        }
        .svg-section h2 {
            color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }
        .svg-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .svg-container svg {
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .features {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .features h3 {
            color: #1e293b;
            font-size: 1.3rem;
            margin-bottom: 15px;
            text-align: center;
        }
        .features ul {
            list-style: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #374151;
        }
        .features li:before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-right: 10px;
        }
        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        .comparison-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
        }
        .comparison-item h4 {
            color: #1e293b;
            font-size: 1.2rem;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Realistic Human Body Design</h1>
            <p>Advanced anatomical proportions with realistic curves and details</p>
        </div>

        <div class="svg-section">
            <h2>Vue Antérieure (Anterior View)</h2>
            <div class="svg-container">
                <object data="/images/postural/anterior-view.svg" type="image/svg+xml" width="600" height="800"></object>
            </div>
        </div>

        <div class="svg-section">
            <h2>Vue Postérieure (Posterior View)</h2>
            <div class="svg-container">
                <object data="/images/postural/posterior-view.svg" type="image/svg+xml" width="600" height="800"></object>
            </div>
        </div>

        <div class="svg-section">
            <h2>Vue Latérale (Lateral View)</h2>
            <div class="svg-container">
                <object data="/images/postural/lateral-view.svg" type="image/svg+xml" width="600" height="800"></object>
            </div>
        </div>

        <div class="features">
            <h3>✨ New Realistic Features</h3>
            <ul>
                <li><strong>Anatomical Proportions:</strong> Proper head-to-body ratio with realistic neck curves</li>
                <li><strong>Natural Body Curves:</strong> Smooth path-based shapes instead of geometric blocks</li>
                <li><strong>Detailed Body Parts:</strong> Realistic shoulders, arms, elbows, forearms, hands, waist, hips, legs, knees, ankles, and feet</li>
                <li><strong>Professional Gradients:</strong> Skin-tone gradients with subtle shadows for depth</li>
                <li><strong>Interactive Landmarks:</strong> Precisely positioned anatomical landmarks for assessment</li>
                <li><strong>Medical Accuracy:</strong> Proper proportions for postural assessment and analysis</li>
                <li><strong>Beautiful Design:</strong> Elegant styling with professional medical aesthetics</li>
                <li><strong>Scalable Graphics:</strong> High-quality SVG that scales perfectly at any size</li>
            </ul>
        </div>

        <div class="comparison">
            <div class="comparison-item">
                <h4>Before: Geometric Design</h4>
                <p>• Simple rectangles and circles</p>
                <p>• Basic geometric shapes</p>
                <p>• Limited anatomical detail</p>
                <p>• Unrealistic proportions</p>
            </div>
            <div class="comparison-item">
                <h4>After: Realistic Human Body</h4>
                <p>• Natural curves and contours</p>
                <p>• Anatomically accurate proportions</p>
                <p>• Detailed body parts</p>
                <p>• Professional medical appearance</p>
            </div>
        </div>
    </div>
</body>
</html> 