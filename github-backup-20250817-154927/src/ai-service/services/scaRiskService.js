const logger = require('../utils/logger');
const { v4: uuidv4 } = require('uuid');
const moment = require('moment');

/**
 * SCA Risk Assessment Service
 * Simulates AI-powered Sudden Cardiac Arrest risk assessment
 */
class SCARiskService {
  constructor() {
    this.assessmentHistory = new Map();
    this.alertConfigs = new Map();
  }

  /**
   * Assess SCA risk for an athlete
   * @param {Object} data - Assessment data
   * @param {string} data.patientId - Patient/Athlete ID
   * @param {string} data.ecgData - ECG data
   * @param {Object} data.hrv - Heart Rate Variability data
   * @param {Object} data.additionalData - Additional patient data
   * @returns {Object} Assessment result
   */
  async assessRisk({ patientId, ecgData, hrv, additionalData }) {
    try {
      logger.info(`Starting SCA risk assessment for patient: ${patientId}`);

      // Simulate AI processing time
      await this.simulateProcessing();

      // Analyze ECG data
      const ecgAnalysis = this.analyzeECG(ecgData);
      
      // Analyze HRV data
      const hrvAnalysis = this.analyzeHRV(hrv);
      
      // Calculate risk factors
      const riskFactors = this.calculateRiskFactors(additionalData);
      
      // Generate risk score (0.0 to 1.0)
      const riskScore = this.calculateRiskScore(ecgAnalysis, hrvAnalysis, riskFactors);
      
      // Determine confidence level
      const confidence = this.calculateConfidence(ecgAnalysis, hrvAnalysis);
      
      // Generate explanation
      const explanation = this.generateExplanation(ecgAnalysis, hrvAnalysis, riskFactors, riskScore);
      
      // Generate recommendation
      const recommendation = this.generateRecommendation(riskScore, explanation);
      
      // Create assessment result
      const assessment = {
        patientId,
        riskScore: parseFloat(riskScore.toFixed(2)),
        confidence: parseFloat(confidence.toFixed(2)),
        explanation,
        recommendation,
        timestamp: new Date().toISOString(),
        assessmentId: uuidv4(),
        metadata: {
          ecgAnalysis,
          hrvAnalysis,
          riskFactors,
          modelVersion: '1.0.0',
          processingTime: Math.random() * 2 + 0.5 // 0.5-2.5 seconds
        }
      };

      // Store assessment in history
      this.storeAssessment(patientId, assessment);

      // Check for alerts
      await this.checkAlerts(patientId, assessment);

      logger.info(`SCA risk assessment completed for patient: ${patientId}, score: ${riskScore}`);

      return assessment;

    } catch (error) {
      logger.error(`Error in SCA risk assessment for patient ${patientId}:`, error);
      throw new Error(`SCA risk assessment failed: ${error.message}`);
    }
  }

  /**
   * Analyze ECG data
   * @param {string} ecgData - ECG data
   * @returns {Object} ECG analysis results
   */
  analyzeECG(ecgData) {
    // Simulate ECG analysis
    const analysis = {
      qtInterval: Math.random() * 100 + 300, // 300-400ms
      qtcInterval: Math.random() * 50 + 400, // 400-450ms
      tWaveMorphology: this.getRandomTWaveMorphology(),
      stSegment: this.getRandomSTSegment(),
      arrhythmias: this.detectArrhythmias(ecgData),
      conductionAbnormalities: this.detectConductionAbnormalities(ecgData)
    };

    return analysis;
  }

  /**
   * Analyze Heart Rate Variability data
   * @param {Object} hrv - HRV data
   * @returns {Object} HRV analysis results
   */
  analyzeHRV(hrv = {}) {
    const analysis = {
      rmssd: hrv.rmssd || Math.random() * 100 + 20, // 20-120ms
      sdnn: hrv.sdnn || Math.random() * 150 + 30, // 30-180ms
      pnn50: hrv.pnn50 || Math.random() * 30 + 5, // 5-35%
      meanHR: hrv.meanHR || Math.random() * 40 + 50, // 50-90 bpm
      hrvStatus: this.assessHRVStatus(hrv)
    };

    return analysis;
  }

  /**
   * Calculate risk factors from additional data
   * @param {Object} additionalData - Additional patient data
   * @returns {Object} Risk factors
   */
  calculateRiskFactors(additionalData = {}) {
    const factors = {
      age: additionalData.age || 25,
      gender: additionalData.gender || 'male',
      familyHistory: additionalData.familyHistory || false,
      previousCardiacEvents: additionalData.previousCardiacEvents || false,
      medications: additionalData.medications || [],
      symptoms: additionalData.symptoms || [],
      bmi: this.calculateBMI(additionalData.height, additionalData.weight)
    };

    return factors;
  }

  /**
   * Calculate overall risk score
   * @param {Object} ecgAnalysis - ECG analysis
   * @param {Object} hrvAnalysis - HRV analysis
   * @param {Object} riskFactors - Risk factors
   * @returns {number} Risk score (0.0-1.0)
   */
  calculateRiskScore(ecgAnalysis, hrvAnalysis, riskFactors) {
    let score = 0.0;

    // ECG factors (40% weight)
    if (ecgAnalysis.qtcInterval > 440) score += 0.2;
    if (ecgAnalysis.tWaveMorphology === 'inverted') score += 0.15;
    if (ecgAnalysis.stSegment === 'elevated') score += 0.1;
    if (ecgAnalysis.arrhythmias.length > 0) score += 0.1;

    // HRV factors (30% weight)
    if (hrvAnalysis.rmssd < 30) score += 0.15;
    if (hrvAnalysis.sdnn < 50) score += 0.1;
    if (hrvAnalysis.hrvStatus === 'poor') score += 0.05;

    // Risk factors (30% weight)
    if (riskFactors.familyHistory) score += 0.1;
    if (riskFactors.previousCardiacEvents) score += 0.15;
    if (riskFactors.age > 35) score += 0.05;
    if (riskFactors.symptoms.includes('chest pain')) score += 0.1;

    return Math.min(score, 1.0);
  }

  /**
   * Calculate confidence level
   * @param {Object} ecgAnalysis - ECG analysis
   * @param {Object} hrvAnalysis - HRV analysis
   * @returns {number} Confidence (0.0-1.0)
   */
  calculateConfidence(ecgAnalysis, hrvAnalysis) {
    let confidence = 0.8; // Base confidence

    // Adjust based on data quality
    if (ecgAnalysis.qtcInterval && hrvAnalysis.rmssd) {
      confidence += 0.1;
    }

    // Add some randomness to simulate real-world variability
    confidence += (Math.random() - 0.5) * 0.1;

    return Math.max(0.7, Math.min(0.95, confidence));
  }

  /**
   * Generate explanation for the assessment
   * @param {Object} ecgAnalysis - ECG analysis
   * @param {Object} hrvAnalysis - HRV analysis
   * @param {Object} riskFactors - Risk factors
   * @param {number} riskScore - Risk score
   * @returns {string} Explanation
   */
  generateExplanation(ecgAnalysis, hrvAnalysis, riskFactors, riskScore) {
    const explanations = [];

    if (ecgAnalysis.qtcInterval > 440) {
      explanations.push('Prolonged QTc interval detected');
    }

    if (ecgAnalysis.tWaveMorphology === 'inverted') {
      explanations.push('Abnormal T-wave morphology observed');
    }

    if (hrvAnalysis.rmssd < 30) {
      explanations.push('Reduced heart rate variability (RMSSD)');
    }

    if (riskFactors.familyHistory) {
      explanations.push('Family history of cardiac events');
    }

    if (riskFactors.previousCardiacEvents) {
      explanations.push('Previous cardiac events in medical history');
    }

    if (explanations.length === 0) {
      explanations.push('No significant risk factors detected');
    }

    return explanations.join('. ');
  }

  /**
   * Generate recommendation based on risk score
   * @param {number} riskScore - Risk score
   * @param {string} explanation - Explanation
   * @returns {string} Recommendation
   */
  generateRecommendation(riskScore, explanation) {
    if (riskScore >= 0.8) {
      return 'Urgent review by a sports cardiologist recommended. Immediate cardiac evaluation required.';
    } else if (riskScore >= 0.6) {
      return 'Cardiac evaluation recommended within 1-2 weeks. Monitor for symptoms.';
    } else if (riskScore >= 0.4) {
      return 'Regular cardiac monitoring recommended. Follow up in 3-6 months.';
    } else {
      return 'Continue regular medical surveillance. No immediate intervention required.';
    }
  }

  /**
   * Store assessment in history
   * @param {string} patientId - Patient ID
   * @param {Object} assessment - Assessment result
   */
  storeAssessment(patientId, assessment) {
    if (!this.assessmentHistory.has(patientId)) {
      this.assessmentHistory.set(patientId, []);
    }
    
    this.assessmentHistory.get(patientId).push(assessment);
    
    // Keep only last 50 assessments per patient
    if (this.assessmentHistory.get(patientId).length > 50) {
      this.assessmentHistory.get(patientId).shift();
    }
  }

  /**
   * Get assessment history for a patient
   * @param {string} patientId - Patient ID
   * @param {Object} options - Query options
   * @returns {Array} Assessment history
   */
  async getAssessmentHistory(patientId, options = {}) {
    const history = this.assessmentHistory.get(patientId) || [];
    const { limit = 10, offset = 0 } = options;
    
    return history
      .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
      .slice(offset, offset + limit);
  }

  /**
   * Get analytics data
   * @param {Object} options - Analytics options
   * @returns {Object} Analytics data
   */
  async getAnalytics(options = {}) {
    const { teamId, dateRange, riskThreshold = 0.5 } = options;
    
    // Simulate analytics data
    return {
      totalAssessments: Math.floor(Math.random() * 1000) + 100,
      highRiskCases: Math.floor(Math.random() * 50) + 5,
      averageRiskScore: (Math.random() * 0.3 + 0.2).toFixed(2),
      riskDistribution: {
        low: Math.floor(Math.random() * 60) + 20,
        medium: Math.floor(Math.random() * 30) + 10,
        high: Math.floor(Math.random() * 10) + 2
      },
      trends: {
        weekly: this.generateTrendData(7),
        monthly: this.generateTrendData(30)
      }
    };
  }

  /**
   * Configure alerts for a patient
   * @param {Object} config - Alert configuration
   * @returns {Object} Alert configuration
   */
  async configureAlerts(config) {
    const { patientId, threshold = 0.6, notificationSettings = {} } = config;
    
    const alertConfig = {
      patientId,
      threshold,
      notificationSettings: {
        email: notificationSettings.email || false,
        sms: notificationSettings.sms || false,
        push: notificationSettings.push || true
      },
      enabled: true,
      createdAt: new Date().toISOString()
    };

    this.alertConfigs.set(patientId, alertConfig);
    
    return alertConfig;
  }

  /**
   * Check for alerts based on assessment
   * @param {string} patientId - Patient ID
   * @param {Object} assessment - Assessment result
   */
  async checkAlerts(patientId, assessment) {
    const config = this.alertConfigs.get(patientId);
    
    if (config && config.enabled && assessment.riskScore >= config.threshold) {
      logger.warn(`SCA Risk Alert: Patient ${patientId} has risk score ${assessment.riskScore} (threshold: ${config.threshold})`);
      
      // In a real implementation, this would trigger notifications
      // await this.sendAlertNotification(patientId, assessment, config);
    }
  }

  // Helper methods
  simulateProcessing() {
    return new Promise(resolve => setTimeout(resolve, Math.random() * 1000 + 500));
  }

  getRandomTWaveMorphology() {
    const morphologies = ['normal', 'inverted', 'flattened', 'biphasic'];
    return morphologies[Math.floor(Math.random() * morphologies.length)];
  }

  getRandomSTSegment() {
    const segments = ['normal', 'elevated', 'depressed'];
    return segments[Math.floor(Math.random() * segments.length)];
  }

  detectArrhythmias(ecgData) {
    // Simulate arrhythmia detection
    const arrhythmias = [];
    if (Math.random() > 0.8) arrhythmias.push('premature ventricular contractions');
    if (Math.random() > 0.9) arrhythmias.push('atrial fibrillation');
    return arrhythmias;
  }

  detectConductionAbnormalities(ecgData) {
    // Simulate conduction abnormality detection
    const abnormalities = [];
    if (Math.random() > 0.85) abnormalities.push('left bundle branch block');
    if (Math.random() > 0.9) abnormalities.push('right bundle branch block');
    return abnormalities;
  }

  assessHRVStatus(hrv) {
    if (hrv.rmssd < 30) return 'poor';
    if (hrv.rmssd < 50) return 'fair';
    return 'good';
  }

  calculateBMI(height, weight) {
    if (!height || !weight) return null;
    const heightInMeters = height / 100;
    return (weight / (heightInMeters * heightInMeters)).toFixed(1);
  }

  generateTrendData(days) {
    const data = [];
    for (let i = 0; i < days; i++) {
      data.push({
        date: moment().subtract(i, 'days').format('YYYY-MM-DD'),
        riskScore: (Math.random() * 0.3 + 0.2).toFixed(2),
        assessments: Math.floor(Math.random() * 10) + 1
      });
    }
    return data.reverse();
  }
}

module.exports = new SCARiskService(); 