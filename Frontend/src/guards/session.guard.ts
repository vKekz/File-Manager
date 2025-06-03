import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";
import { HOME_ROUTE } from "../constants/route.constants";
import { Injectable } from "@angular/core";

/**
 * Represents the guard that makes sure that authenticated users cannot view the login/signup route.
 */
@Injectable({
  providedIn: "root",
})
export class SessionGuard implements CanActivate {
  constructor(
    private readonly authController: AuthController,
    private readonly router: Router
  ) {}

  async canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean | UrlTree> {
    return !this.authController.isAuthenticated() ? true : this.router.parseUrl(HOME_ROUTE);
  }
}
