import { Injectable, signal, WritableSignal } from "@angular/core";
import { ApiResponse } from "../contracts/api.response";
import { AuthResponse } from "../contracts/auth.response";
import { AuthService } from "../services/auth.service";
import { UserDto } from "../dtos/user.dto";

@Injectable({
  providedIn: "root",
})
export class AuthController {
  private readonly ACCESS_TOKEN_KEY: string = "access_token";
  private readonly USER_KEY: string = "user";

  public readonly apiResponse: WritableSignal<ApiResponse | null> = signal(null);

  constructor(private readonly authService: AuthService) {}

  public async registerUser(username: string, email: string, password: string) {
    const response = await this.authService.registerUser(username, email, password);
    return this.handleResponse(response);
  }

  public async loginUser(email: string, password: string) {
    const response = await this.authService.loginUser(email, password);
    return this.handleResponse(response);
  }

  public async validate() {
    const accessToken = localStorage.getItem(this.ACCESS_TOKEN_KEY);
    if (!accessToken) {
      return;
    }

    const response = await this.authService.validate(accessToken);
    return this.handleResponse(response);
  }

  public isAuthenticated(): boolean {
    return this.getUser() !== null;
  }

  public getUser(): UserDto | null {
    const rawUser = sessionStorage.getItem(this.USER_KEY);
    if (!rawUser) {
      return null;
    }

    return JSON.parse(rawUser) as UserDto;
  }

  private handleResponse(response: ApiResponse | AuthResponse): ApiResponse | AuthResponse {
    if ("message" in response) {
      this.apiResponse.set(response);

      localStorage.removeItem(this.ACCESS_TOKEN_KEY);
      sessionStorage.removeItem(this.USER_KEY);
    } else {
      this.apiResponse.set(null);

      localStorage.setItem(this.ACCESS_TOKEN_KEY, response.accessToken);
      sessionStorage.setItem(this.USER_KEY, JSON.stringify(response.user));
    }

    return response;
  }
}
