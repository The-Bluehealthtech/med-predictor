// Simple test to verify credentials
const validCredentials = [
  // Player credentials
  { email: 'ahmed.bensalah@clubafricain.tn', password: 'player123', role: 'player', playerId: 4 },
  { email: 'ahmed.bensalah1@fifa.com', password: 'Joueur123!', role: 'player', playerId: 4 },
  // Admin credentials
  { email: 'admin@medpredictor.com', password: 'password123', role: 'admin' },
  { email: 'club.admin@medpredictor.com', password: 'password123', role: 'admin' },
  { email: 'club.manager@medpredictor.com', password: 'password123', role: 'admin' },
  { email: 'association.admin@medpredictor.com', password: 'password123', role: 'admin' },
  { email: 'referee@medpredictor.com', password: 'password123', role: 'admin' }
];

function testCredentials(email, password) {
  const matchedCredential = validCredentials.find(
    valid => valid.email === email && valid.password === password
  );
  
  console.log('Testing credentials:', { email, password });
  console.log('Match found:', !!matchedCredential);
  if (matchedCredential) {
    console.log('Role:', matchedCredential.role);
    console.log('Player ID:', matchedCredential.playerId);
  }
  return matchedCredential;
}

// Test the player credentials
console.log('=== Testing Player Credentials ===');
testCredentials('ahmed.bensalah@clubafricain.tn', 'player123');
testCredentials('ahmed.bensalah1@fifa.com', 'Joueur123!');

console.log('\n=== Testing Admin Credentials ===');
testCredentials('admin@medpredictor.com', 'password123');

console.log('\n=== Testing Invalid Credentials ===');
testCredentials('invalid@email.com', 'wrongpassword');

