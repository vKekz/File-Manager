import { ApplicationConfig, InjectionToken, provideZoneChangeDetection } from "@angular/core";
import { provideRouter } from "@angular/router";

import { routes } from "./app.routes";

export const BASE_URL_API = new InjectionToken("BASE_URL_API");

const baseUrlProvider = {
  provide: BASE_URL_API,
  useValue: `${window.location.host}/File-Manager/Backend/public/api/`,
  deps: [],
};

export const appConfig: ApplicationConfig = {
  providers: [provideZoneChangeDetection({ eventCoalescing: true }), provideRouter(routes), baseUrlProvider],
};
