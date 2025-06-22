import { Injectable } from "@angular/core";
import { UserService } from "../services/user.service";
import { UserDto } from "../dtos/user.dto";
import { RouteHandler } from "../handlers/route.handler";
import { APP_ROUTES } from "../constants/route.constants";

@Injectable({ providedIn: "root" })
export class UserController {
  private readonly ACCESS_TOKEN_KEY: string = "access_token";
  private readonly USER_KEY: string = "user";

  constructor(
    private readonly userService: UserService,
    private readonly routeHandler: RouteHandler
  ) {}

  public async changeSettings(user: UserDto) {
    await this.userService.changeSettings(user);

    // Hacky....
    sessionStorage.setItem(this.USER_KEY, JSON.stringify(user));
  }

  public async logout() {
    await this.userService.logout();

    // Hacky....
    localStorage.removeItem(this.ACCESS_TOKEN_KEY);
    sessionStorage.removeItem(this.USER_KEY);

    await this.routeHandler.goToRoute(APP_ROUTES.home);
  }
}
