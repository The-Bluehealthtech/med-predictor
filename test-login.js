// Test the authentication logic
console.log('Testing authentication logic...');

// Simulate the login function
async function testLogin(credentials) {
  console.log('=== LOGIN TEST START ===');
  console.log('Credentials received:', { email: credentials.email, passwordLength: credentials.password?.length });
  
  // Check for missing credentials first
  if (!credentials.email || !credentials.password) {
    console.log('❌ Missing credentials:', { email: !!credentials.email, password: !!credentials.password });
    return { success: false, error: 'Email and password are required' };
  }

  console.log('✅ Valid credentials provided, setting up user...');
  
  try {
    const token = 'test_session_' + Date.now();
    const refreshToken = 'test_refresh_' + Date.now();
    
    const currentUser = {
      id: 4,
      username: credentials.email.split('@')[0],
      email: credentials.email,
      first_name: 'Ahmed',
      last_name: 'Ben Salah',
      role: 'player',
      avatar: 'https://via.placeholder.com/150x150/2E86AB/FFFFFF?text=ABS'
    };
    
    const currentTenant = {
      id: 4,
      name: 'Ahmed Ben Salah',
      slug: 'ahmed-bensalah',
      display_name: 'Player Portal',
      type: 'player',
      status: 'active',
      country: 'TN',
      timezone: 'Africa/Tunis',
      language: 'en',
      logo: 'https://via.placeholder.com/100x50/2E86AB/FFFFFF?text=ABS',
      settings: {},
      metadata: {},
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };

    console.log('✅ User and tenant data set:', { 
      user: currentUser, 
      tenant: currentTenant,
      token: token 
    });

    console.log('🔍 Checking authentication status:', !!(token && currentUser));
    console.log('=== LOGIN SUCCESS ===');
    return { success: true };
    
  } catch (setupError) {
    console.error('❌ Error during user setup:', setupError);
    return { success: false, error: 'Failed to set up user data' };
  }
}

// Test with the failing credentials
console.log('Testing with ahmed.bensalah@clubafricain.tn / player123');
testLogin({ email: 'ahmed.bensalah@clubafricain.tn', password: 'player123' }).then(result => {
  console.log('Final result:', result);
});

console.log('Testing with any credentials');
testLogin({ email: 'test@test.com', password: 'anypassword' }).then(result => {
  console.log('Final result:', result);
});

