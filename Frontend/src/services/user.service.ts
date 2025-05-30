import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { API_ROUTE_USER } from "../constants/api-routes.constants";

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
}
