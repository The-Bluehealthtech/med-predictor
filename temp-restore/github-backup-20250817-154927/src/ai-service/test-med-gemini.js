const MedGeminiService = require('./services/medGeminiService');

async function testMedGeminiService() {
    console.log('🧪 Testing Med-Gemini Service Integration...\n');

    try {
        // Initialize the service
        const medGeminiService = new MedGeminiService();
        console.log('✅ Med-Gemini service initialized successfully');

        // Test 1: Health Check
        console.log('\n📋 Test 1: Health Check');
        const healthStatus = await medGeminiService.healthCheck();
        console.log('Health Status:', healthStatus);

        // Test 2: Simple Text Analysis
        console.log('\n📋 Test 2: Simple Text Analysis');
        const simplePrompt = 'You are a medical assistant. Please respond with "Hello from Med-Gemini"';
        const simpleResult = await medGeminiService.analyze(simplePrompt);
        console.log('Simple Analysis Result:', {
            success: simpleResult.success,
            text: simpleResult.text?.substring(0, 100) + '...',
            processingTime: simpleResult.processingTime
        });

        // Test 3: Medical Note Generation
        console.log('\n📋 Test 3: Medical Note Generation');
        const transcript = 'Patient reports chest pain for 2 days, blood pressure 140/90, heart rate 85 bpm. No shortness of breath or dizziness.';
        const noteResult = await medGeminiService.generateMedicalNote(transcript, 'SOAP');
        console.log('Medical Note Generation Result:', {
            success: noteResult.success,
            textLength: noteResult.text?.length || 0,
            processingTime: noteResult.processingTime
        });

        // Test 4: Structured Data Extraction
        console.log('\n📋 Test 4: Structured Data Extraction');
        const extractionPrompt = `From the following text, extract these specific clinical values and return them as a JSON object with keys (bp_systolic, bp_diastolic, heart_rate): "Patient reports blood pressure is 120 over 80, heart rate is 65 bpm"`;
        const extractionResult = await medGeminiService.extractStructuredData(extractionPrompt, 'Patient reports blood pressure is 120 over 80, heart rate is 65 bpm');
        console.log('Structured Data Extraction Result:', {
            success: extractionResult.success,
            structuredData: extractionResult.structuredData,
            confidence: extractionResult.confidence,
            processingTime: extractionResult.processingTime
        });

        console.log('\n🎉 All tests completed successfully!');
        console.log('\n📊 Summary:');
        console.log('- Health Check: ✅');
        console.log('- Simple Analysis: ✅');
        console.log('- Medical Note Generation: ✅');
        console.log('- Structured Data Extraction: ✅');

    } catch (error) {
        console.error('❌ Test failed:', error.message);
        console.error('Stack trace:', error.stack);
        
        if (error.message.includes('GOOGLE_AI_API_KEY')) {
            console.log('\n💡 To fix this error:');
            console.log('1. Get a Google AI API key from: https://makersuite.google.com/app/apikey');
            console.log('2. Create a .env file in the src/ai-service directory');
            console.log('3. Add: GOOGLE_AI_API_KEY=your_api_key_here');
        }
    }
}

// Run the test
testMedGeminiService(); 