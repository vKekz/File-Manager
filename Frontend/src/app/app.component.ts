import { Component, OnInit } from "@angular/core";
import { Router, RouterOutlet } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";
import { FooterNavComponent } from "./components/footer-nav/footer-nav.component";
import { LOGIN_ROUTE, SIGNUP_ROUTE } from "../constants/route.constants";

@Component({
  selector: "app-root",
  imports: [RouterOutlet, FooterNavComponent],
  templateUrl: "./app.component.html",
  styleUrl: "./app.component.css",
})
export class AppComponent implements OnInit {
  constructor(private readonly authController: AuthController) {}

  async ngOnInit() {
    await this.authController.validate();
  }

  protected canShowFooter() {
    return (
      location.pathname !== "/" &&
      !location.pathname.startsWith(`${LOGIN_ROUTE}`) &&
      !location.pathname.startsWith(`${SIGNUP_ROUTE}`)
    );
  }
}
