import { Component, OnInit } from "@angular/core";
import { RouterOutlet } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";
import { BottomNavComponent } from "./components/bottom-nav/bottom-nav.component";
import { APP_ROUTES } from "../constants/route.constants";
import { RouteHandler } from "../services/route.handler";

@Component({
  selector: "app-root",
  imports: [RouterOutlet, BottomNavComponent],
  templateUrl: "./app.component.html",
  styleUrl: "./app.component.css",
})
export class AppComponent implements OnInit {
  constructor(
    private readonly authController: AuthController,
    private readonly routeHandler: RouteHandler
  ) {}

  async ngOnInit() {
    await this.authController.validate();
  }

  protected canShowBottomNav() {
    return (
      !this.routeHandler.isOnRoute(APP_ROUTES.home) &&
      !this.routeHandler.isOnRoute(APP_ROUTES.login) &&
      !this.routeHandler.isOnRoute(APP_ROUTES.signup)
    );
  }
}
