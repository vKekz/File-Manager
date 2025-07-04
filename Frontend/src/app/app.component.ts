import { Component, OnInit } from "@angular/core";
import { AuthController } from "../controllers/auth.controller";
import { MainPageComponent } from "./components/main-page/main-page.component";
import { RouteHandler } from "../handlers/route.handler";
import { APP_ROUTES } from "../constants/route.constants";
import { RouterOutlet } from "@angular/router";

@Component({
  selector: "app-root",
  imports: [MainPageComponent, RouterOutlet],
  templateUrl: "./app.component.html",
  styleUrl: "./app.component.css",
})
export class AppComponent implements OnInit {
  constructor(
    private readonly authController: AuthController,
    private readonly routeHandler: RouteHandler
  ) {}

  async ngOnInit() {
    await this.authController.validateSession();
  }

  protected isMainPage() {
    return (
      !this.routeHandler.isOnRoute(APP_ROUTES.home) &&
      !this.routeHandler.isOnRoute(APP_ROUTES.login) &&
      !this.routeHandler.isOnRoute(APP_ROUTES.signup)
    );
  }
}
