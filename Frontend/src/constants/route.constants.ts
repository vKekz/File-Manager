export const APP_ROUTES = {
  home: "",
  login: "login",
  signup: "signup",
  account: "account",
  storage: "storage",
} as const;

export const API_ROUTES = {
  auth: "auth",
  directory: "directory",
} as const;

export type AppRoute = (typeof APP_ROUTES)[keyof typeof APP_ROUTES];
export type ApiRoute = (typeof API_ROUTES)[keyof typeof API_ROUTES];
