# DoctorSignOff Component Integration Guide

## Overview

The `DoctorSignOff.vue` component provides a complete medical assessment signature system with digital signature capture, legal declarations, and secure authentication. This guide shows the exact integration pattern you requested.

## Files Created/Modified

1. **`resources/js/components/DoctorSignOff.vue`** - Main component (already exists)
2. **`resources/js/components/MedicalAssessment.vue`** - Complete integration example
3. **`resources/views/medical-assessment-demo.blade.php`** - Demo page
4. **`DOCTORSIGNOFF_INTEGRATION.md`** - This documentation

## Integration Pattern

### 1. Import du Composant

```javascript
import DoctorSignOff from "./components/DoctorSignOff.vue";
```

### 2. Utilisation dans le Template

```vue
<template>
    <div>
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
</template>
```

### 3. Gestion de l'Événement

```javascript
const handleSigned = (data) => {
    console.log("Signed data:", data);
    // Envoyer les données au serveur
};
```

## Component Props

| Prop              | Type   | Required | Description                                  |
| ----------------- | ------ | -------- | -------------------------------------------- |
| `playerName`      | String | Yes      | Name of the player being assessed            |
| `fitnessDecision` | String | Yes      | Fitness decision (FIT, NOT_FIT, CONDITIONAL) |
| `examinationDate` | String | Yes      | Date of medical examination                  |
| `assessmentId`    | String | Yes      | Unique assessment identifier                 |
| `clinicalNotes`   | String | No       | Clinical notes and observations              |
| `doctorName`      | String | Yes      | Name of the signing doctor                   |
| `licenseNumber`   | String | Yes      | Doctor's medical license number              |

## Emitted Events

### `@signed`

Emitted when the doctor completes the signature process. Returns an object with:

```javascript
{
  signedBy: "Dr. Sarah Smith",
  licenseNumber: "MED-12345",
  signedAt: "2024-01-15T10:30:00.000Z",
  signatureImage: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  fitnessStatus: "FIT",
  assessmentId: "PCMA-1234",
  playerName: "John Doe",
  examinationDate: "2024-01-15",
  ipAddress: "192.168.1.100"
}
```

## Complete Integration Example

### Vue Component Setup

```vue
<template>
    <div class="medical-assessment-container">
        <!-- Assessment Form -->
        <div class="assessment-form">
            <input v-model="playerName" placeholder="Player Name" />
            <select v-model="fitnessDecision">
                <option value="FIT">FIT</option>
                <option value="NOT_FIT">NOT_FIT</option>
                <option value="CONDITIONAL">CONDITIONAL</option>
            </select>
            <!-- Other form fields... -->
        </div>

        <!-- Doctor Sign-Off Component -->
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
</template>

<script>
import { ref } from "vue";
import DoctorSignOff from "./components/DoctorSignOff.vue";

export default {
    name: "MedicalAssessment",
    components: {
        DoctorSignOff,
    },
    setup() {
        // Reactive data
        const playerName = ref("");
        const fitnessDecision = ref("");
        const examinationDate = ref("");
        const assessmentId = ref("");
        const clinicalNotes = ref("");
        const doctorName = ref("");
        const licenseNumber = ref("");

        // Event handler
        const handleSigned = (data) => {
            console.log("Signed data:", data);

            // Send data to server
            sendToServer(data);
        };

        // Server communication
        const sendToServer = async (data) => {
            try {
                const response = await fetch("/api/medical-assessments", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(data),
                });

                if (response.ok) {
                    console.log("Assessment saved successfully");
                } else {
                    console.error("Failed to save assessment");
                }
            } catch (error) {
                console.error("Error saving assessment:", error);
            }
        };

        return {
            playerName,
            fitnessDecision,
            examinationDate,
            assessmentId,
            clinicalNotes,
            doctorName,
            licenseNumber,
            handleSigned,
        };
    },
};
</script>
```

## Component Features

### ✅ Digital Signature Capture

-   Canvas-based signature drawing
-   Touch and mouse support
-   Signature validation and confirmation
-   Clear and redraw functionality

### ✅ Legal Declaration

-   Mandatory checkbox for medical responsibility
-   Professional medical language
-   Legal compliance requirements

### ✅ Security Features

-   Re-authentication modal
-   Password confirmation
-   IP address tracking
-   Timestamp recording

### ✅ Data Validation

-   Complete form validation
-   Required field checking
-   Fitness decision validation
-   Assessment ID uniqueness

### ✅ Professional UI

-   Medical-themed design
-   Responsive layout
-   Accessibility features
-   Professional color scheme

## Server Integration

### API Endpoint Example

```php
// routes/api.php
Route::post('/medical-assessments', [MedicalAssessmentController::class, 'store']);

// app/Http/Controllers/MedicalAssessmentController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'signedBy' => 'required|string',
        'licenseNumber' => 'required|string',
        'signedAt' => 'required|date',
        'signatureImage' => 'required|string',
        'fitnessStatus' => 'required|in:FIT,NOT_FIT,CONDITIONAL',
        'assessmentId' => 'required|string|unique:medical_assessments',
        'playerName' => 'required|string',
        'examinationDate' => 'required|date',
        'ipAddress' => 'required|ip'
    ]);

    $assessment = MedicalAssessment::create($validated);

    return response()->json([
        'success' => true,
        'assessment' => $assessment
    ]);
}
```

## Usage Examples

### Basic Usage

```vue
<DoctorSignOff
    :player-name="'John Doe'"
    :fitness-decision="'FIT'"
    :examination-date="'2024-01-15'"
    :assessment-id="'PCMA-1234'"
    :clinical-notes="'Complete examination performed'"
    :doctor-name="'Dr. Sarah Smith'"
    :license-number="'MED-12345'"
    @signed="handleSigned"
/>
```

### With Dynamic Data

```vue
<template>
    <DoctorSignOff
        :player-name="assessment.playerName"
        :fitness-decision="assessment.fitnessDecision"
        :examination-date="assessment.examinationDate"
        :assessment-id="assessment.id"
        :clinical-notes="assessment.clinicalNotes"
        :doctor-name="currentDoctor.name"
        :license-number="currentDoctor.licenseNumber"
        @signed="saveAssessment"
    />
</template>
```

## Testing

### Demo Page

Visit `/medical-assessment-demo` to see the component in action.

### Manual Testing

1. Fill in all required fields
2. Accept legal declaration
3. Draw signature on canvas
4. Confirm signature
5. Enter password for re-authentication
6. Verify data is emitted correctly

## Security Considerations

-   All sensitive data is validated server-side
-   CSRF protection is included
-   Password re-authentication for critical actions
-   IP address tracking for audit trails
-   Signature image is base64 encoded and validated

## Browser Compatibility

-   Modern browsers with Canvas API support
-   Touch devices for signature capture
-   JavaScript ES6+ support
-   Vue 3 compatibility

## Dependencies

-   Vue 3
-   Tailwind CSS (for styling)
-   Canvas API (built-in browser support)

## Troubleshooting

### Common Issues

1. **Signature not capturing**: Check if canvas is properly initialized
2. **Event not firing**: Verify all required props are provided
3. **Styling issues**: Ensure Tailwind CSS is loaded
4. **Server errors**: Check CSRF token and validation rules

### Debug Mode

Enable console logging to debug issues:

```javascript
const handleSigned = (data) => {
    console.log("Signed data:", data);
    console.log("Assessment ID:", data.assessmentId);
    console.log("Doctor:", data.signedBy);
    // ... rest of handler
};
```

## Support

For issues or questions about the DoctorSignOff component integration, refer to:

-   Component source code: `resources/js/components/DoctorSignOff.vue`
-   Demo page: `resources/views/medical-assessment-demo.blade.php`
-   Integration example: `resources/js/components/MedicalAssessment.vue`
