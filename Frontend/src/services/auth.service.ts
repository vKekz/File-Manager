import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { ApiResponse } from "../contracts/api.response";
import { firstValueFrom } from "rxjs";
import { AuthResponse } from "../contracts/auth.response";
import { API_ROUTES } from "../constants/route.constants";

@Injectable({ providedIn: "root" })
export class AuthService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.auth}`;
  }

  public registerUser(username: string, email: string, password: string) {
    const route = `${this.endpoint}/register`;
    const payload = {
      username: username,
      email: email,
      password: password,
    } as const;
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, payload);
    return firstValueFrom(request);
  }

  public loginUser(email: string, password: string) {
    const route = `${this.endpoint}/login`;
    const payload = {
      email: email,
      password: password,
    } as const;
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, payload);
    return firstValueFrom(request);
  }
}
