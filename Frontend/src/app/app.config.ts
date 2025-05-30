import { ApplicationConfig, InjectionToken, provideZoneChangeDetection } from "@angular/core";
import { provideRouter } from "@angular/router";
import { routes } from "./app.routes";
import { provideHttpClient } from "@angular/common/http";

export const API_URL = new InjectionToken("API_URL");

const apiUrlProvider = {
  provide: API_URL,
  useValue: `http://${location.hostname}/File-Manager/Backend/public/api`,
  deps: [],
};

export const appConfig: ApplicationConfig = {
  providers: [
    apiUrlProvider,
    provideHttpClient(),
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
  ],
};
