import { Inject, Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { API_URL } from "../app/app.config";
import { ApiResponse } from "../contracts/api.response";
import { firstValueFrom } from "rxjs";
import { AuthResponse } from "../contracts/auth.response";
import { API_ROUTES } from "../constants/route.constants";

@Injectable({ providedIn: "root" })
export class SessionService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.session}`;
  }

  public validate($accessToken: string) {
    const route = `${this.endpoint}/validate`;
    const request = this.httpClient.post<AuthResponse | ApiResponse>(route, "", {
      headers: {
        Authorization: `Bearer ${$accessToken}`,
      },
    });
    return firstValueFrom(request);
  }
}
