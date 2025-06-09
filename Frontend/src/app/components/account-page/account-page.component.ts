import { Component } from "@angular/core";
import { AuthController } from "../../../controllers/auth.controller";

@Component({
  selector: "app-account-page",
  imports: [],
  templateUrl: "./account-page.component.html",
  styleUrl: "./account-page.component.css",
})
export class AccountPageComponent {
  constructor(protected readonly authController: AuthController) {}
}
