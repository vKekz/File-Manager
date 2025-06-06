export const APP_ROUTES = {
  home: "",
  login: "login",
  signup: "signup",
  profile: "profile",
  storage: "storage",
};

export const API_ROUTES = {
  auth: "auth",
  directory: "directory",
};

export type AppRoute = (typeof APP_ROUTES)[keyof typeof APP_ROUTES];
export type ApiRoute = (typeof API_ROUTES)[keyof typeof API_ROUTES];
