export type RootStackParamList = {
  Login: undefined;
  TenantSelection: undefined;
  MainTabs: undefined;
};

export type TabParamList = {
  Dashboard: undefined;
  Profile: undefined;
  Health: undefined;
  Licenses: undefined;
  Performance: undefined;
  Settings: undefined;
};

export type PlayerProfileStackParamList = {
  PlayerProfile: { playerId: number };
  HealthRecords: { playerId: number };
  Licenses: { playerId: number };
  Performance: { playerId: number };
};


