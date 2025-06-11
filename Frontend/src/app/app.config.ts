import { ApplicationConfig, importProvidersFrom, InjectionToken, provideZoneChangeDetection } from "@angular/core";
import { provideRouter } from "@angular/router";
import { routes } from "./app.routes";
import { HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi } from "@angular/common/http";
import { AuthInterceptor } from "../interceptors/auth.interceptor";
import { NgxsModule } from "@ngxs/store";
import { DirectoryState } from "../states/directory/directory.state";
import { FileState } from "../states/file/file.state";

export const API_URL = new InjectionToken("API_URL");

const apiUrlProvider = {
  provide: API_URL,
  useValue: `http://${location.hostname}/File-Manager/Backend/public/api`,
  deps: [],
};

const authInterceptorProvider = {
  provide: HTTP_INTERCEPTORS,
  useClass: AuthInterceptor,
  multi: true,
};

export const appConfig: ApplicationConfig = {
  providers: [
    authInterceptorProvider,
    apiUrlProvider,
    importProvidersFrom(NgxsModule.forRoot([DirectoryState, FileState])),
    provideHttpClient(withInterceptorsFromDi()),
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
  ],
};
