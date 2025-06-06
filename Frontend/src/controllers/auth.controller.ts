import { Injectable, signal, WritableSignal } from "@angular/core";
import { ApiResponse } from "../contracts/api.response";
import { AuthResponse } from "../contracts/auth.response";
import { AuthService } from "../services/auth.service";
import { UserDto } from "../dtos/user.dto";
import { RouteHandler } from "../handlers/route.handler";
import { APP_ROUTES } from "../constants/route.constants";

@Injectable({
  providedIn: "root",
})
export class AuthController {
  private readonly ACCESS_TOKEN_KEY: string = "access_token";
  private readonly USER_KEY: string = "user";

  public readonly apiResponse: WritableSignal<ApiResponse | null> = signal(null);

  constructor(
    private readonly authService: AuthService,
    private readonly routeHandler: RouteHandler
  ) {}

  public async registerUser(username: string, email: string, password: string) {
    const response = await this.authService.registerUser(username, email, password);
    await this.handleResponse(response, true);
    return response;
  }

  public async loginUser(email: string, password: string) {
    const response = await this.authService.loginUser(email, password);
    await this.handleResponse(response, true);
    return response;
  }

  public async validate() {
    const accessToken = this.getAccessToken();
    if (!accessToken) {
      return;
    }

    const response = await this.authService.validate(accessToken);
    if (!(await this.handleResponse(response))) {
      await this.routeHandler.goToRoute(APP_ROUTES.home);
    }

    return response;
  }

  public isAuthenticated(): boolean {
    return this.getUser() !== null && this.getAccessToken() !== null;
  }

  public getUser(): UserDto | null {
    const rawUser = sessionStorage.getItem(this.USER_KEY);
    if (!rawUser) {
      return null;
    }

    return JSON.parse(rawUser) as UserDto;
  }

  public getAccessToken(): string | null {
    return localStorage.getItem(this.ACCESS_TOKEN_KEY);
  }

  public resetResponse() {
    this.apiResponse.set(null);
  }

  private async handleResponse(response: ApiResponse | AuthResponse, routeOnSuccess: boolean = false) {
    if ("message" in response) {
      this.apiResponse.set(response);

      localStorage.removeItem(this.ACCESS_TOKEN_KEY);
      sessionStorage.removeItem(this.USER_KEY);
      return false;
    }

    this.resetResponse();

    localStorage.setItem(this.ACCESS_TOKEN_KEY, response.accessToken);
    sessionStorage.setItem(this.USER_KEY, JSON.stringify(response.user));

    if (routeOnSuccess) {
      await this.routeHandler.goToRoute(APP_ROUTES.storage);
    }

    return true;
  }
}
