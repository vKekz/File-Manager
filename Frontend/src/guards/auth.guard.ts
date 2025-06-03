import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";
import { HOME_ROUTE } from "../constants/route.constants";
import { Injectable } from "@angular/core";

/**
 * Represents the guard that makes sure that only authenticated users can view the given route.
 */
@Injectable({
  providedIn: "root",
})
export class AuthGuard implements CanActivate {
  constructor(
    private readonly authController: AuthController,
    private readonly router: Router
  ) {}

  async canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean | UrlTree> {
    return this.authController.isAuthenticated() ? true : this.router.parseUrl(HOME_ROUTE);
  }
}
