import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { API_ROUTE_USER } from "../constants/api-routes.constants";
import { ApiResponse } from "../contracts/api/api.response";
import { firstValueFrom } from "rxjs";
import { SessionResponse } from "../contracts/session.response";

@Injectable({ providedIn: "root" })
export class UserService {
  private readonly userEndpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.userEndpoint = `${apiUrl}${API_ROUTE_USER}`;
  }

  public async registerUser(username: string, email: string, password: string) {
    const route = `${this.userEndpoint}/auth/register`;
    const payload = {
      username: username,
      email: email,
      password: password,
    };
    const request = this.httpClient.post<SessionResponse | ApiResponse>(route, payload);
    return await firstValueFrom(request);
  }

  public async loginUser(email: string, password: string) {
    const route = `${this.userEndpoint}/auth/login`;
    const payload = {
      email: email,
      password: password,
    };
    const request = this.httpClient.post<SessionResponse | ApiResponse>(route, payload);
    return await firstValueFrom(request);
  }
}
