import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { API_ROUTE_USER } from "../constants/api-routes.constants";
import { RegisterResponse } from "../contracts/register.response";
import { ApiResponse } from "../contracts/api/api.response";
import { firstValueFrom } from "rxjs";

@Injectable({ providedIn: "root" })
export class UserService {
  private readonly userEndpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.userEndpoint = `${apiUrl}${API_ROUTE_USER}`;
  }

  public getUsers() {
    const route = `${this.userEndpoint}/list`;
    return this.httpClient.get(route);
  }

  public async registerUser(username: string, email: string, password: string) {
    const route = `${this.userEndpoint}/auth/register`;
    const payload = {
      username: username,
      email: email,
      password: password,
    };
    const response = this.httpClient.post<RegisterResponse | ApiResponse>(route, payload);
    return await firstValueFrom(response);
  }

  public async loginUser(email: string, password: string) {
    const route = `${this.userEndpoint}/auth/login`;
    const payload = {
      email: email,
      password: password,
    };
    const response = this.httpClient.post(route, payload);
    return await firstValueFrom(response);
  }
}
