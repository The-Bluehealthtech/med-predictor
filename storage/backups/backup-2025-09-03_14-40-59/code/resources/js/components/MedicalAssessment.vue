<template>
  <div class="max-w-6xl mx-auto bg-gray-50 min-h-screen p-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">🏥 Medical Assessment System</h1>
      <p class="text-gray-600">Complete medical fitness assessment with digital signature</p>
    </div>

    <!-- Assessment Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">📋 Assessment Details</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Player Name</label>
          <input 
            v-model="playerName" 
            type="text" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter player name"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Fitness Decision</label>
          <select 
            v-model="fitnessDecision" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Select decision</option>
            <option value="FIT">FIT</option>
            <option value="NOT_FIT">NOT_FIT</option>
            <option value="CONDITIONAL">CONDITIONAL</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Examination Date</label>
          <input 
            v-model="examinationDate" 
            type="date" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Assessment ID</label>
          <input 
            v-model="assessmentId" 
            type="text" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter assessment ID"
          >
        </div>
        
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Clinical Notes</label>
          <textarea 
            v-model="clinicalNotes" 
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter clinical notes..."
          ></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Doctor Name</label>
          <input 
            v-model="doctorName" 
            type="text" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter doctor name"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
          <input 
            v-model="licenseNumber" 
            type="text" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter license number"
          >
        </div>
      </div>
    </div>

    <!-- Doctor Sign-Off Component -->
    <div v-if="showSignOff" class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">✍️ Doctor Sign-Off</h2>
      
      <!-- 1. Import du Composant -->
      <!-- import DoctorSignOff from './components/DoctorSignOff.vue' -->
      
      <!-- 2. Utilisation dans le Template -->
      <DoctorSignOff 
        :player-name="playerName" 
        :fitness-decision="fitnessDecision" 
        :examination-date="examinationDate" 
        :assessment-id="assessmentId" 
        :clinical-notes="clinicalNotes" 
        :doctor-name="doctorName" 
        :license-number="licenseNumber" 
        @signed="handleSigned" 
      />
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="flex justify-between items-center">
        <button 
          @click="showSignOff = !showSignOff"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200"
        >
          {{ showSignOff ? 'Hide' : 'Show' }} Doctor Sign-Off
        </button>
        
        <button 
          @click="loadSampleData"
          class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition duration-200"
        >
          Load Sample Data
        </button>
      </div>
    </div>

    <!-- Results Display -->
    <div v-if="signedData" class="bg-white rounded-xl shadow-lg p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">✅ Signed Assessment Results</h2>
      
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-green-900 mb-3">📋 Signed Data</h3>
        <pre class="text-sm text-gray-700 overflow-x-auto">{{ JSON.stringify(signedData, null, 2) }}</pre>
      </div>
      
      <!-- 3. Gestion de l'Événement -->
      <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="font-semibold text-blue-900 mb-2">Event Handler Example:</h4>
        <code class="text-sm text-blue-800">
          const handleSigned = (data) => {<br>
          &nbsp;&nbsp;console.log('Signed data:', data);<br>
          &nbsp;&nbsp;// Envoyer les données au serveur<br>
          }
        </code>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import DoctorSignOff from './DoctorSignOff.vue'

export default {
  name: 'MedicalAssessment',
  components: {
    DoctorSignOff
  },
  setup() {
    // Reactive data for the form
    const playerName = ref('')
    const fitnessDecision = ref('')
    const examinationDate = ref('')
    const assessmentId = ref('')
    const clinicalNotes = ref('')
    const doctorName = ref('')
    const licenseNumber = ref('')
    
    // Component state
    const showSignOff = ref(false)
    const signedData = ref(null)

    // Load sample data for demonstration
    const loadSampleData = () => {
      playerName.value = 'John Doe'
      fitnessDecision.value = 'FIT'
      examinationDate.value = new Date().toISOString().split('T')[0]
      assessmentId.value = 'PCMA-' + Math.floor(Math.random() * 9000) + 1000
      clinicalNotes.value = 'Complete medical examination performed. All parameters within normal ranges. Player is medically fit for competition.'
      doctorName.value = 'Dr. Sarah Smith'
      licenseNumber.value = 'MED-' + Math.floor(Math.random() * 90000) + 10000
      showSignOff.value = true
    }

    // 3. Gestion de l'Événement
    const handleSigned = (data) => {
      console.log('Signed data:', data)
      signedData.value = data
      
      // Envoyer les données au serveur
      // Example: sendToServer(data)
      
      // Show success message
      alert('✅ Medical assessment signed successfully!\n\nAssessment ID: ' + data.assessmentId + '\nSigned by: ' + data.signedBy + '\nTimestamp: ' + data.signedAt)
    }

    // Example function to send data to server
    const sendToServer = async (data) => {
      try {
        const response = await fetch('/api/medical-assessments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify(data)
        })
        
        if (response.ok) {
          console.log('Assessment saved successfully')
        } else {
          console.error('Failed to save assessment')
        }
      } catch (error) {
        console.error('Error saving assessment:', error)
      }
    }

    return {
      // Form data
      playerName,
      fitnessDecision,
      examinationDate,
      assessmentId,
      clinicalNotes,
      doctorName,
      licenseNumber,
      
      // Component state
      showSignOff,
      signedData,
      
      // Methods
      loadSampleData,
      handleSigned,
      sendToServer
    }
  }
}
</script>

<style scoped>
pre {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 0.375rem;
  padding: 1rem;
  font-family: 'Courier New', monospace;
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style> 