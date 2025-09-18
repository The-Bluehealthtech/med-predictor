/**
 * FHIR R4 Patient Model
 * Représente un patient dans le système FIT
 */

const { DataTypes } = require('sequelize');

const Patient = {
  id: {
    type: DataTypes.UUID,
    primaryKey: true,
    defaultValue: DataTypes.UUIDV4
  },
  
  // FHIR Patient Resource Fields
  resourceType: {
    type: DataTypes.STRING,
    defaultValue: 'Patient',
    allowNull: false
  },
  
  identifier: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of identifiers (SSN, Medical Record Number, etc.)'
  },
  
  active: {
    type: DataTypes.BOOLEAN,
    defaultValue: true,
    allowNull: false
  },
  
  name: {
    type: DataTypes.JSONB,
    allowNull: false,
    comment: 'Array of human names (family, given, prefix, suffix)'
  },
  
  telecom: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of contact details (phone, email, etc.)'
  },
  
  gender: {
    type: DataTypes.ENUM('male', 'female', 'other', 'unknown'),
    allowNull: false
  },
  
  birthDate: {
    type: DataTypes.DATEONLY,
    allowNull: false
  },
  
  address: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of addresses'
  },
  
  maritalStatus: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Marital status coding'
  },
  
  multipleBirth: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Multiple birth indicator or number'
  },
  
  photo: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of patient photos'
  },
  
  contact: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of contact persons'
  },
  
  communication: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of language preferences'
  },
  
  generalPractitioner: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of general practitioners'
  },
  
  managingOrganization: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Organization that manages the patient'
  },
  
  link: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of links to other patients'
  },
  
  // FIT Specific Fields
  fitPatientId: {
    type: DataTypes.STRING,
    unique: true,
    allowNull: false,
    comment: 'FIT internal patient ID'
  },
  
  preferredLanguage: {
    type: DataTypes.STRING,
    defaultValue: 'fr',
    allowNull: false
  },
  
  emergencyContact: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Emergency contact information'
  },
  
  insuranceInfo: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Insurance information'
  },
  
  medicalHistory: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Summary of medical history'
  },
  
  allergies: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Array of known allergies'
  },
  
  medications: {
    type: DataTypes.JSONB,
    allowNull: true,
    comment: 'Current medications'
  },
  
  // Audit fields
  createdAt: {
    type: DataTypes.DATE,
    allowNull: false,
    defaultValue: DataTypes.NOW
  },
  
  updatedAt: {
    type: DataTypes.DATE,
    allowNull: false,
    defaultValue: DataTypes.NOW
  },
  
  createdBy: {
    type: DataTypes.UUID,
    allowNull: true,
    comment: 'User who created the patient record'
  },
  
  updatedBy: {
    type: DataTypes.UUID,
    allowNull: true,
    comment: 'User who last updated the patient record'
  }
};

module.exports = Patient;
