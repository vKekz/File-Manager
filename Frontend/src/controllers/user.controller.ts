import { Injectable } from "@angular/core";
import { UserService } from "../services/user.service";
import { UserDto } from "../dtos/user.dto";

@Injectable({ providedIn: "root" })
export class UserController {
  private readonly USER_KEY: string = "user";

  constructor(private readonly userService: UserService) {}

  public async changeSettings(user: UserDto) {
    await this.userService.changeSettings(user);

    // Hacky....
    sessionStorage.setItem(this.USER_KEY, JSON.stringify(user));
  }
}
