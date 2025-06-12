import { Injectable, signal, WritableSignal } from "@angular/core";
import { ApiResponse } from "../contracts/api.response";
import { AuthResponse } from "../contracts/auth.response";
import { AuthService } from "../services/auth.service";
import { UserDto } from "../dtos/user.dto";
import { RouteHandler } from "../handlers/route.handler";
import { APP_ROUTES } from "../constants/route.constants";
import { Store } from "@ngxs/store";
import { FetchDirectories } from "../states/directory/directory.actions";
import { SessionService } from "../services/session.service";

@Injectable({
  providedIn: "root",
})
export class AuthController {
  private readonly ACCESS_TOKEN_KEY: string = "access_token";
  private readonly USER_KEY: string = "user";

  public readonly apiResponse: WritableSignal<ApiResponse | null> = signal(null);

  constructor(
    private readonly authService: AuthService,
    private readonly sessionService: SessionService,
    private readonly routeHandler: RouteHandler,
    private readonly store: Store
  ) {}

  public async registerUser(username: string, email: string, password: string) {
    const response = await this.authService.registerUser(username, email, password);
    await this.handleResponse(response, true);
  }

  public async loginUser(email: string, password: string) {
    const response = await this.authService.loginUser(email, password);
    await this.handleResponse(response, true);
  }

  public async validateSession() {
    const accessToken = this.getAccessToken();
    if (!accessToken) {
      return;
    }

    const response = await this.sessionService.validate(accessToken);
    const isResponseSuccessful = await this.handleResponse(response);
    if (!isResponseSuccessful) {
      await this.routeHandler.goToRoute(APP_ROUTES.home);
    }
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

  private async handleResponse(response: ApiResponse | AuthResponse, rerouteOnSuccess: boolean = false) {
    if ("message" in response) {
      this.apiResponse.set(response);
      this.clearStorage();
      return false;
    }

    this.resetResponse();

    localStorage.setItem(this.ACCESS_TOKEN_KEY, response.accessToken);
    sessionStorage.setItem(this.USER_KEY, JSON.stringify(response.user));

    // Pre-fetch directories to reduce loading time
    this.store.dispatch(new FetchDirectories());

    if (rerouteOnSuccess) {
      await this.routeHandler.goToRoute(APP_ROUTES.storage);
    }

    return true;
  }

  private clearStorage() {
    localStorage.removeItem(this.ACCESS_TOKEN_KEY);
    sessionStorage.removeItem(this.USER_KEY);
  }
}
