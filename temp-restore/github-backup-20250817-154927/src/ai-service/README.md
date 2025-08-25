# FIT Medical AI Service

FIFA-aligned clinical intelligence platform for football medical data management.

## Overview

The FIT Medical AI Service is a Node.js microservice that provides AI-powered medical assessments for the FIT platform. It implements Human-in-the-Loop (HITL) AI workflows where all AI outputs are assistive and require physician review.

## Features

- **SCA Risk Assessment**: AI-powered Sudden Cardiac Arrest risk evaluation
- **Injury Prediction**: Machine learning models for injury risk assessment
- **Medical Note Generation**: AI-assisted medical documentation
- **FIFA Compliance**: Automated compliance checking for medical standards
- **Human-in-the-Loop**: All AI outputs require physician review and approval

## Technology Stack

- **Runtime**: Node.js 18+
- **Framework**: Express.js
- **AI/ML**: TensorFlow.js, OpenAI API, LangChain
- **Security**: JWT authentication, rate limiting, CORS
- **Monitoring**: Winston logging, health checks
- **Validation**: Joi schema validation

## Quick Start

### Prerequisites

- Node.js 18+ 
- npm or yarn
- Redis (optional, for caching)

### Installation

```bash
# Clone the repository
cd src/ai-service

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env

# Start the service
npm run dev
```

### Environment Variables

Create a `.env` file with the following variables:

```env
# Server Configuration
PORT=3001
NODE_ENV=development

# Security
JWT_SECRET=your-super-secret-jwt-key-here
ALLOWED_ORIGINS=http://localhost:3000

# AI Configuration
OPENAI_API_KEY=your-openai-api-key-here
AI_MODEL_VERSION=1.0.0

# External Services
LARAVEL_API_URL=http://localhost:8000/api/v1
```

## API Endpoints

### SCA Risk Assessment

#### POST /api/ai/sca-risk/assess

Assess Sudden Cardiac Arrest risk for an athlete.

**Request Body:**
```json
{
  "patientId": "ATHLETE_001",
  "ecgData": "base64_encoded_ecg_data",
  "hrv": {
    "rmssd": 45.2,
    "sdnn": 78.5,
    "pnn50": 12.3,
    "meanHR": 65.0
  },
  "additionalData": {
    "age": 25,
    "gender": "male",
    "height": 180,
    "weight": 75,
    "familyHistory": false,
    "previousCardiacEvents": false,
    "medications": [],
    "symptoms": []
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "patientId": "ATHLETE_001",
    "riskScore": 0.85,
    "confidence": 0.92,
    "explanation": "Abnormal T-wave inversion detected.",
    "recommendation": "Urgent review by a sports cardiologist recommended.",
    "timestamp": "2024-01-15T10:30:00.000Z",
    "assessmentId": "uuid-here",
    "metadata": {
      "ecgAnalysis": { /* ECG analysis results */ },
      "hrvAnalysis": { /* HRV analysis results */ },
      "riskFactors": { /* Risk factors */ },
      "modelVersion": "1.0.0",
      "processingTime": 1.2
    }
  },
  "timestamp": "2024-01-15T10:30:00.000Z",
  "service": "FIT Medical AI - SCA Risk Assessment"
}
```

### Health Checks

#### GET /health
Basic health check endpoint.

#### GET /health/detailed
Detailed health check with system metrics.

#### GET /health/ready
Kubernetes readiness probe.

#### GET /health/live
Kubernetes liveness probe.

## Architecture

### Service Structure

```
src/ai-service/
├── server.js              # Main application entry point
├── package.json           # Dependencies and scripts
├── routes/                # API route handlers
│   ├── scaRisk.js        # SCA risk assessment endpoints
│   ├── injuryPrediction.js # Injury prediction endpoints
│   ├── medicalNote.js    # Medical note generation endpoints
│   ├── compliance.js     # FIFA compliance endpoints
│   └── health.js         # Health check endpoints
├── services/              # Business logic services
│   ├── scaRiskService.js # SCA risk assessment logic
│   ├── injuryService.js  # Injury prediction logic
│   └── medicalNoteService.js # Medical note generation
├── middleware/            # Express middleware
│   ├── auth.js           # JWT authentication
│   ├── validation.js     # Request validation
│   └── errorHandler.js   # Error handling
├── utils/                 # Utility functions
│   └── logger.js         # Winston logging setup
└── logs/                 # Application logs
```

### AI Workflow

1. **Data Input**: Medical data (ECG, HRV, patient history)
2. **AI Processing**: Machine learning models analyze the data
3. **Risk Assessment**: Generate risk scores and explanations
4. **Human Review**: Physician reviews and approves AI outputs
5. **Documentation**: Store approved assessments in database

## Security

### Authentication
- JWT-based authentication
- Role-based access control (RBAC)
- Medical staff permissions required

### Data Protection
- All medical data encrypted in transit
- HIPAA/GDPR compliant data handling
- Audit trail for all AI assessments

### Rate Limiting
- 100 requests per 15 minutes per IP
- Configurable limits per endpoint

## Development

### Running Tests

```bash
# Run all tests
npm test

# Run tests in watch mode
npm run test:watch

# Run specific test file
npm test -- scaRiskService.test.js
```

### Code Quality

```bash
# Lint code
npm run lint

# Format code
npm run format
```

### Docker

```bash
# Build Docker image
docker build -t fit-medical-ai .

# Run container
docker run -p 3001:3001 fit-medical-ai
```

## Integration

### Laravel Backend Integration

The AI service communicates with the Laravel backend via HTTP API calls:

```javascript
// Example: Send assessment to Laravel
const response = await axios.post(`${process.env.LARAVEL_API_URL}/medical/assessments`, {
  athlete_id: patientId,
  assessment_type: 'sca_risk',
  risk_score: assessment.riskScore,
  confidence: assessment.confidence,
  explanation: assessment.explanation,
  recommendation: assessment.recommendation,
  ai_metadata: assessment.metadata
});
```

### Frontend Integration

The Vue.js frontend communicates with the AI service:

```javascript
// Example: Request SCA risk assessment
const response = await this.$http.post('/api/ai/sca-risk/assess', {
  patientId: athlete.id,
  ecgData: ecgData,
  hrv: hrvData,
  additionalData: athlete.medicalData
});
```

## Monitoring

### Logging
- Structured JSON logging with Winston
- Log levels: error, warn, info, debug
- Log rotation and compression

### Metrics
- Memory usage monitoring
- CPU utilization tracking
- Request/response time monitoring
- Error rate tracking

### Health Checks
- Service availability monitoring
- Dependency health checks
- Kubernetes probe endpoints

## Deployment

### Production Checklist

- [ ] Set `NODE_ENV=production`
- [ ] Configure secure JWT secret
- [ ] Set up SSL/TLS certificates
- [ ] Configure Redis for caching
- [ ] Set up monitoring and alerting
- [ ] Configure backup strategies
- [ ] Set up CI/CD pipeline

### Kubernetes Deployment

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: fit-medical-ai
spec:
  replicas: 3
  selector:
    matchLabels:
      app: fit-medical-ai
  template:
    metadata:
      labels:
        app: fit-medical-ai
    spec:
      containers:
      - name: fit-medical-ai
        image: fit-medical-ai:latest
        ports:
        - containerPort: 3001
        env:
        - name: NODE_ENV
          value: "production"
        - name: JWT_SECRET
          valueFrom:
            secretKeyRef:
              name: fit-medical-secrets
              key: jwt-secret
        livenessProbe:
          httpGet:
            path: /health/live
            port: 3001
        readinessProbe:
          httpGet:
            path: /health/ready
            port: 3001
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

MIT License - see LICENSE file for details.

## Support

For support and questions:
- Create an issue in the repository
- Contact the FIT Medical Team
- Check the documentation wiki 