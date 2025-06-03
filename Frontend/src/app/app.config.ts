import { ApplicationConfig, InjectionToken, provideZoneChangeDetection } from "@angular/core";
import { provideRouter } from "@angular/router";
import { routes } from "./app.routes";
import { HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi } from "@angular/common/http";
import { AuthInterceptor } from "../interceptors/auth.interceptor";

export const API_URL = new InjectionToken("API_URL");

const apiUrlProvider = {
  provide: API_URL,
  useValue: `http://${location.hostname}/File-Manager/Backend/public/api`,
  deps: [],
};

export const appConfig: ApplicationConfig = {
  providers: [
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true,
    },
    apiUrlProvider,
    provideHttpClient(withInterceptorsFromDi()),
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
  ],
};
