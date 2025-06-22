export interface AppRouteDetails {
  path: string;
  title?: string;
  description?: string;
}

export const APP_ROUTES = {
  home: { path: "" } as AppRouteDetails,
  login: { path: "login" } as AppRouteDetails,
  signup: { path: "signup" } as AppRouteDetails,
  account: {
    path: "account",
    title: "Konto",
    description: "Passe deine Kontoeinstellungen und persönlichen Daten wie Benutzername und E-Mail an.",
  } as AppRouteDetails,
  storage: {
    path: "storage",
    title: "Dateien",
    description: "Zeige und verwalte deine persönlichen Ordner und Dateien. Sicher durch AES-256-GCM Verschlüsselung.",
  } as AppRouteDetails,
} as const;

export const API_ROUTES = {
  auth: "auth",
  session: "session",
  storage: "storage",
  directory: "directory",
  file: "file",
} as const;

export type AppRoute = (typeof APP_ROUTES)[keyof typeof APP_ROUTES];
export type ApiRoute = (typeof API_ROUTES)[keyof typeof API_ROUTES];
