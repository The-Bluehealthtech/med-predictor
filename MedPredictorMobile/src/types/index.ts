// User Types
export interface User {
  id: number;
  name?: string;
  username?: string;
  email: string;
  first_name?: string;
  last_name?: string;
  role: string;
  tenant_id?: number;
  permissions?: string[];
  avatar?: string;
  created_at?: string;
  updated_at?: string;
}

// Tenant Types
export interface Tenant {
  id: number;
  name: string;
  slug: string;
  display_name: string;
  description?: string;
  type: 'association' | 'club' | 'federation' | 'organization' | 'system' | 'player';
  status: 'active' | 'inactive' | 'suspended' | 'pending';
  fifa_connect_id?: string;
  fifa_code?: string;
  country: string;
  timezone: string;
  language: string;
  logo_url?: string;
  logo?: string;
  website?: string;
  email?: string;
  phone?: string;
  address?: string;
  settings: Record<string, any>;
  metadata: Record<string, any>;
  parent_tenant_id?: number;
  created_by?: number;
  updated_by?: number;
  created_at: string;
  updated_at: string;
}

// Player Types
export interface Player {
  id: number;
  tenant_id: number;
  fifa_connect_id?: string;
  name: string;
  first_name: string;
  last_name: string;
  date_of_birth: string;
  nationality: string;
  position: string;
  club_id?: number;
  association_id?: number;
  address?: string;
  contact_phone?: string;
  contact_email?: string;
  legal_guardian?: string;
  school_professional_status?: string;
  parental_consent?: string;
  license_type?: string;
  previous_clubs?: string;
  previous_license_number?: string;
  height?: number;
  weight?: number;
  preferred_foot?: string;
  weak_foot?: string;
  skill_moves?: number;
  international_reputation?: number;
  work_rate?: string;
  body_type?: string;
  real_face?: boolean;
  release_clause_eur?: number;
  player_face_url?: string;
  player_picture?: string;
  club_logo_url?: string;
  club_name?: string;
  nation_flag_url?: string;
  overall_rating?: number;
  potential_rating?: number;
  value_eur?: number;
  wage_eur?: number;
  age?: number;
  contract_valid_until?: string;
  fifa_version?: string;
  last_updated?: string;
  email?: string;
  phone?: string;
  jersey_number?: number;
  // Health & Performance Scores
  ghs_physical_score?: number;
  ghs_mental_score?: number;
  ghs_civic_score?: number;
  ghs_sleep_score?: number;
  ghs_overall_score?: number;
  ghs_color_code?: string;
  ghs_ai_suggestions?: string;
  ghs_last_updated?: string;
  injury_risk_score?: number;
  injury_risk_level?: string;
  injury_risk_reason?: string;
  injury_risk_last_assessed?: string;
  match_availability?: boolean;
  last_availability_update?: string;
  contribution_score?: number;
  matches_contributed?: number;
  training_sessions_logged?: number;
  health_records_contributed?: number;
  created_at: string;
  updated_at: string;
}

// Health Record Types
export interface HealthRecord {
  id: number;
  tenant_id: number;
  user_id: number;
  player_id: number;
  record_date: string;
  visit_type: string;
  chief_complaint?: string;
  diagnosis?: string;
  treatment_plan?: string;
  medications?: string;
  follow_up_required?: boolean;
  follow_up_date?: string;
  risk_score?: number;
  status: string;
  notes?: string;
  attachments?: string;
  created_at: string;
  updated_at: string;
}

// License Types
export interface PlayerLicense {
  id: number;
  tenant_id: number;
  player_id: number;
  license_type: string;
  license_number: string;
  status: string;
  issue_date: string;
  expiry_date: string;
  club_id?: number;
  transfer_status?: string;
  contract_type?: string;
  contract_start_date?: string;
  contract_end_date?: string;
  medical_clearance?: boolean;
  international_clearance?: boolean;
  created_at: string;
  updated_at: string;
}

// Performance Types
export interface PlayerPerformance {
  id: number;
  tenant_id: number;
  player_id: number;
  performance_date: string;
  overall_performance_score: number;
  endurance_score: number;
  strength_score: number;
  speed_score: number;
  agility_score: number;
  technical_score: number;
  tactical_score: number;
  mental_score: number;
  social_score: number;
  notes?: string;
  created_at: string;
  updated_at: string;
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
}

export interface AuthResponse {
  success: boolean;
  token: string;
  refresh_token: string;
  user: User;
  tenant: Tenant;
}

export interface TenantListResponse {
  success: boolean;
  data: Tenant[];
}

export interface PlayerDataResponse {
  success: boolean;
  data: {
    identity: Player;
    club?: any;
    contract?: any;
    fifa_ratings?: any;
    market_value?: any;
    health_score?: any;
    injury_risk?: any;
    availability?: any;
    recent_performances?: PlayerPerformance[];
    recent_health_records?: HealthRecord[];
    recent_pcma?: any[];
    medical_predictions?: any[];
    licenses?: PlayerLicense[];
    passport?: any;
    seasonal_stats?: any[];
    recent_match_events?: any[];
    statistics?: any;
  };
}

// Context Types
export interface AuthContextType {
  user: User | null;
  tenant: Tenant | null;
  loading: boolean;
  login: (credentials: LoginCredentials) => Promise<{ success: boolean; error?: string }>;
  logout: () => Promise<void>;
  hasPermission: (permission: string) => boolean;
  refreshToken: () => Promise<boolean>;
}

export interface TenantContextType {
  currentTenant: Tenant | null;
  availableTenants: Tenant[];
  loading: boolean;
  switchTenant: (tenantId: number) => Promise<{ success: boolean; error?: string }>;
  refreshTenants: () => Promise<void>;
}

export interface PlayerDataContextType {
  playerData: Player | null;
  healthRecords: HealthRecord[];
  licenses: PlayerLicense[];
  performances: PlayerPerformance[];
  loading: boolean;
  isError: boolean;
  refreshPlayerData: (playerId: number) => Promise<void>;
  refreshHealthRecords: (playerId: number) => Promise<void>;
  refreshLicenses: (playerId: number) => Promise<void>;
  refreshPerformances: (playerId: number) => Promise<void>;
}

// Login Credentials
export interface LoginCredentials {
  email: string;
  password: string;
  tenant_id?: number;
}

// App Configuration
export interface AppConfig {
  apiBaseUrl: string;
  apiVersion: string;
  enableBiometrics: boolean;
  enableOfflineMode: boolean;
  defaultTenantId?: number;
}

