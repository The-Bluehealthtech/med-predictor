// Test the API endpoint for player ID 4
const fetch = require('node-fetch');

async function testAPI() {
  console.log('🧪 Testing API endpoint for player ID 4...');
  
  try {
    const response = await fetch('http://localhost/api/players/4', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    });

    console.log('📡 Response status:', response.status);
    console.log('📡 Response ok:', response.ok);
    
    if (response.ok) {
      const data = await response.json();
      console.log('✅ API Response:', JSON.stringify(data, null, 2));
      
      if (data.success && data.data) {
        console.log('🎯 Player data found:');
        console.log(`   - Name: ${data.data.first_name} ${data.data.last_name}`);
        console.log(`   - Position: ${data.data.position}`);
        console.log(`   - Club: ${data.data.club?.name}`);
        console.log(`   - Overall Rating: ${data.data.overall_rating}`);
        console.log(`   - GHS Overall Score: ${data.data.ghs_overall_score}`);
        console.log(`   - Value: €${data.data.value_eur?.toLocaleString()}`);
      }
    } else {
      console.log('❌ API request failed:', response.statusText);
    }
  } catch (error) {
    console.error('❌ API test failed:', error.message);
  }
}

testAPI();

