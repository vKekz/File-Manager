import { HttpEvent, HttpHandler, HttpInterceptor, HttpRequest } from "@angular/common/http";
import { Observable } from "rxjs";
import { Inject, Injectable } from "@angular/core";
import { AuthController } from "../controllers/auth.controller";
import { API_URL } from "../app/app.config";

/**
 * Represents the HTTP interceptor that makes sure that requests to the REST API are send with the authorization header if available.
 */
@Injectable({
  providedIn: "root",
})
export class AuthInterceptor implements HttpInterceptor {
  constructor(
    @Inject(API_URL) private readonly apiUrl: string,
    private readonly authController: AuthController
  ) {}

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const url = req.url;
    const accessToken = this.authController.getAccessToken();
    if (url.startsWith(this.apiUrl) || !accessToken) {
      return next.handle(req);
    }

    const authorizedReq = req.clone({
      setHeaders: {
        Authorization: `Bearer ${accessToken}`,
      },
    });
    return next.handle(authorizedReq);
  }
}
