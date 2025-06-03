import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { API_ROUTE_AUTH } from "../constants/api-route.constants";
import { ApiResponse } from "../contracts/api.response";
import { firstValueFrom } from "rxjs";
import { AuthResponse } from "../contracts/auth.response";

@Injectable({ providedIn: "root" })
export class AuthService {
  private readonly userEndpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.userEndpoint = `${apiUrl}${API_ROUTE_AUTH}`;
  }

  public async registerUser(username: string, email: string, password: string) {
    const route = `${this.userEndpoint}/register`;
    const payload = {
      username: username,
      email: email,
      password: password,
    };
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, payload);
    return await firstValueFrom(request);
  }

  public async loginUser(email: string, password: string) {
    const route = `${this.userEndpoint}/login`;
    const payload = {
      email: email,
      password: password,
    };
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, payload);
    return await firstValueFrom(request);
  }

  public async validate($accessToken: string) {
    const route = `${this.userEndpoint}/validate`;
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, "", {
      headers: {
        Authorization: `Bearer ${$accessToken}`,
      },
    });
    return await firstValueFrom(request);
  }
}
