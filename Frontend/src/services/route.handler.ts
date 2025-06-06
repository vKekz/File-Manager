import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { APP_ROUTES, AppRoute } from "../constants/route.constants";

@Injectable({ providedIn: "root" })
export class RouteHandler {
  constructor(private readonly router: Router) {}

  public isOnRoute(route: AppRoute) {
    const path = `/${route}`;
    if (route == APP_ROUTES.home) {
      return location.pathname === path;
    }

    return location.pathname.startsWith(path);
  }

  public goToRoute(route: AppRoute) {
    return this.router.navigateByUrl(`/${route}`);
  }
}
