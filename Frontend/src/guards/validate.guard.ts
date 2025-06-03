import { ActivatedRouteSnapshot, CanActivate, RouterStateSnapshot, UrlTree } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";
import { Injectable } from "@angular/core";

/**
 * Represents the guard that makes sure that stored access tokens are validated before other requests.
 */
@Injectable({
  providedIn: "root",
})
export class ValidateGuard implements CanActivate {
  constructor(private readonly authController: AuthController) {}

  // TODO: Not the best approach, but for now it works. Because this is done before the auth guard.
  async canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean | UrlTree> {
    if (this.authController.getUser()) {
      return true;
    }

    await this.authController.validate();
    return true;
  }
}
