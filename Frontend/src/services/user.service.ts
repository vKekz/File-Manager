import { Inject, Injectable } from "@angular/core";
import { API_URL } from "../app/app.config";
import { HttpClient } from "@angular/common/http";
import { API_ROUTES } from "../constants/route.constants";
import { UserDto } from "../dtos/user.dto";
import { firstValueFrom } from "rxjs";
import { UserSettings } from "../dtos/user/user-settings";

@Injectable({ providedIn: "root" })
export class UserService {
  private readonly endpoint: string;

  constructor(
    @Inject(API_URL) apiUrl: string,
    private readonly httpClient: HttpClient
  ) {
    this.endpoint = `${apiUrl}/${API_ROUTES.user}`;
  }

  public changeSettings(user: UserDto) {
    const route = `${this.endpoint}/settings`;
    const payload = {
      id: user.id,
      settings: user.settings,
    };
    const request = this.httpClient.patch<UserSettings>(route, payload);
    return firstValueFrom(request);
  }
}
